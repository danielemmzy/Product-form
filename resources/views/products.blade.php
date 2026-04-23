<!DOCTYPE html>
<html>
<head>
    <title>Product Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="p-4">

<div class="container">

    <h3>Product Inventory</h3>

    <!-- FORM -->
    <form id="productForm" class="mb-3">
        <input id="product_name" name="product_name"
               class="form-control mb-2"
               placeholder="Product Name">

        <input id="quantity" name="quantity"
               type="number"
               class="form-control mb-2"
               placeholder="Quantity">

        <input id="price" name="price"
               type="number"
               step="0.01"
               class="form-control mb-2"
               placeholder="Price">
        <button id="submitBtn" class="btn btn-primary">Add</button>
    </form>

    <!-- TABLE -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Date</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody id="tableBody">
        @php $grand = 0; @endphp

        @foreach($products as $p)
            @php $grand += $p->quantity * $p->price; @endphp

            <tr id="row-{{ $p->id }}">
    <td class="col-name">{{ $p->product_name }}</td>
    <td class="col-qty">{{ $p->quantity }}</td>
    <td class="col-price">{{ $p->price }}</td>
    <td>{{ $p->created_at }}</td>
    <td class="col-total">{{ $p->quantity * $p->price }}</td>
    <td>
        <button class="btn btn-sm btn-warning"
                onclick="startEdit({{ $p->id }})">
            Edit
        </button>
    </td>
</tr>
        @endforeach
        </tbody>

        <tfoot>
        <tr>
            <th colspan="4">Grand Total</th>
            <th id="grand">{{ $grand }}</th>
            <th></th>
        </tr>
        </tfoot>
    </table>

</div>

<script src="{{ asset('js/products.js') }}"></script>

</body>
</html>