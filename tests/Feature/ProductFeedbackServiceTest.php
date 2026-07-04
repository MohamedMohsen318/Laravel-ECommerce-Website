<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductComment;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\User\ProductCommentService;
use App\Services\User\ProductReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tests\TestCase;

class ProductFeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_requires_completed_purchase(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();

        $this->expectHttpExceptionStatus(403, function () use ($item, $user) {
            app(ProductReviewService::class)->storeOrUpdate($item, $user->id, [
                'rating' => 5,
                'body' => 'Great product',
            ]);
        });

        $this->createCompletedOrderFor($user, $item);

        $review = app(ProductReviewService::class)->storeOrUpdate($item, $user->id, [
            'rating' => 4,
            'body' => 'Still good',
        ]);

        $this->assertSame($user->id, $review->user_id);
        $this->assertSame($item->id, $review->item_id);
    }

    public function test_review_rejects_inactive_product(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem(['is_active' => false]);

        $this->createCompletedOrderFor($user, $item);

        $this->expectHttpExceptionStatus(404, function () use ($item, $user) {
            app(ProductReviewService::class)->storeOrUpdate($item, $user->id, [
                'rating' => 5,
            ]);
        });
    }

    public function test_comment_requires_completed_purchase(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();

        $this->expectHttpExceptionStatus(403, function () use ($item, $user) {
            app(ProductCommentService::class)->store($item, $user->id, [
                'body' => 'Nice',
            ]);
        });

        $this->createCompletedOrderFor($user, $item);

        $comment = app(ProductCommentService::class)->store($item, $user->id, [
            'body' => 'Nice',
        ]);

        $this->assertSame($user->id, $comment->user_id);
        $this->assertSame($item->id, $comment->item_id);
    }

    public function test_comment_rejects_inactive_product(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem(['is_active' => false]);

        $this->createCompletedOrderFor($user, $item);

        $this->expectHttpExceptionStatus(404, function () use ($item, $user) {
            app(ProductCommentService::class)->store($item, $user->id, [
                'body' => 'Nice',
            ]);
        });
    }

    public function test_repeated_review_updates_existing_row(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();

        $this->createCompletedOrderFor($user, $item);

        app(ProductReviewService::class)->storeOrUpdate($item, $user->id, [
            'rating' => 2,
            'body' => 'First version',
        ]);

        $review = app(ProductReviewService::class)->storeOrUpdate($item, $user->id, [
            'rating' => 5,
            'body' => 'Updated version',
        ]);

        $this->assertSame(1, ProductReview::count());
        $this->assertSame(5, $review->refresh()->rating);
        $this->assertSame('Updated version', $review->body);
    }

    public function test_deleting_review_only_removes_current_users_review(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $item = $this->createItem();

        $this->createCompletedOrderFor($firstUser, $item);
        $this->createCompletedOrderFor($secondUser, $item);

        app(ProductReviewService::class)->storeOrUpdate($item, $firstUser->id, [
            'rating' => 3,
            'body' => 'First user',
        ]);
        $secondReview = app(ProductReviewService::class)->storeOrUpdate($item, $secondUser->id, [
            'rating' => 4,
            'body' => 'Second user',
        ]);

        app(ProductReviewService::class)->destroy($item, $firstUser->id);

        $this->assertSame(1, ProductReview::count());
        $this->assertDatabaseMissing('product_reviews', [
            'item_id' => $item->id,
            'user_id' => $firstUser->id,
        ]);
        $this->assertDatabaseHas('product_reviews', [
            'id' => $secondReview->id,
            'user_id' => $secondUser->id,
        ]);
    }

    public function test_deleting_parent_comment_cascades_to_replies(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();

        $this->createCompletedOrderFor($user, $item);

        $parent = app(ProductCommentService::class)->store($item, $user->id, [
            'body' => 'Parent',
        ]);
        $reply = app(ProductCommentService::class)->store($item, $user->id, [
            'parent_id' => $parent->id,
            'body' => 'Reply',
        ]);

        app(ProductCommentService::class)->destroy($parent);

        $this->assertDatabaseMissing('product_comments', ['id' => $parent->id]);
        $this->assertDatabaseMissing('product_comments', ['id' => $reply->id]);
    }

    public function test_user_cannot_update_or_delete_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = $this->createItem();

        $comment = ProductComment::create([
            'item_id' => $item->id,
            'user_id' => $owner->id,
            'body' => 'Owner comment',
        ]);

        $this->actingAs($otherUser)
            ->put(route('comments.update', $comment), ['body' => 'Changed'])
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('comments.destroy', $comment))
            ->assertForbidden();

        $this->assertSame('Owner comment', $comment->refresh()->body);
        $this->assertDatabaseHas('product_comments', ['id' => $comment->id]);
    }

    private function createItem(array $attributes = []): Item
    {
        return Item::create([
            'price' => $attributes['price'] ?? 100,
            'is_active' => $attributes['is_active'] ?? true,
            'stock' => $attributes['stock'] ?? 10,
            'sku' => $attributes['sku'] ?? uniqid('sku-', true),
        ]);
    }

    private function createCompletedOrderFor(User $user, Item $item): void
    {
        $order = Order::create([
            'user_id' => $user->id,
            'status' => OrderStatus::COMPLETED,
            'total_price' => 100,
            'final_total' => 100,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'price' => 100,
        ]);
    }

    private function expectHttpExceptionStatus(int $status, callable $callback): void
    {
        try {
            $callback();
        } catch (HttpExceptionInterface $exception) {
            $this->assertSame($status, $exception->getStatusCode());

            return;
        }

        $this->fail("Expected HTTP exception with status {$status}.");
    }
}
