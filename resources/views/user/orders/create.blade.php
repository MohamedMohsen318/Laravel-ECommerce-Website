@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
    <section class="card stack">
        <h1>Create Order</h1>

        <form class="form" method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div id="order-items" class="stack">
                <div class="grid">
                    <input type="number" name="items[0][item_id]" placeholder="Product ID">
                    <input type="number" name="items[0][quantity]" placeholder="Quantity">
                </div>
            </div>

            <div class="actions">
                <button class="button secondary" type="button" onclick="addItem()">Add Product</button>
                <button class="button" type="submit">Confirm Order</button>
            </div>
        </form>
    </section>

    <script>
        let index = 1;
        function addItem() {
            const row = document.createElement('div');
            row.className = 'grid';
            row.innerHTML = `
                <input type="number" name="items[${index}][item_id]" placeholder="Product ID">
                <input type="number" name="items[${index}][quantity]" placeholder="Quantity">
            `;
            document.getElementById('order-items').appendChild(row);
            index++;
        }
    </script>
@endsection
