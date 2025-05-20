@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Stock Images</h4>
                        <a href="{{ route('admin.stock_images.add') }}" class="btn btn-primary mb-3">Add New Image</a>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row">
                            @foreach($images as $image)
                            <div class="col-md-3 text-center"> <!-- Added text-center here -->
                                <div class="image-container position-relative">
                                    <img src="{{ asset('public/storage/stock_images/' . $image->image) }}" alt="Stock Image">
                                    <button class="btn btn-danger delete-image" data-id="{{ $image->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <div class="status-toggle">
                                        <input type="checkbox" class="toggle-status" data-id="{{ $image->id }}" {{ $image->status == 'Active' ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <h5 class="mt-2">{{ $image->title }}</h5> <!-- Removed extra div and adjusted margin -->
                            </div>
                        @endforeach

                        </div>

                        @if($images->isEmpty())
                            <p class="text-center text-muted">No stock images available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }
        .delete-image {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            background-color: rgba(255, 0, 0, 0.8);
            border: none;
            padding: 5px 8px;
            cursor: pointer;
        }
        .image-container:hover .delete-image {
            display: block;
        }
        .status-toggle {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.delete-image').click(function() {
                let imageId = $(this).data('id');
                let deleteUrl = "{{ route('admin.stock_images.delete', ':id') }}".replace(':id', imageId);

                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this image!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: deleteUrl,
                            type: "DELETE",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.success) {
                                    location.reload();
                                } else {
                                    swal("Error deleting image!", { icon: "error" });
                                }
                            }
                        });
                    }
                });
            });

            $('.toggle-status').change(function() {
                let imageId = $(this).data('id');
                let statusUrl = "{{ route('admin.stock_images.status', ':id') }}".replace(':id', imageId);

                $.post(statusUrl, { _token: "{{ csrf_token() }}" }, function(response) {
                    swal("Status Updated!", { icon: "success" });
                });
            });
        });
    </script>
@endsection
