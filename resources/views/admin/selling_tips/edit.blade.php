@extends('admin.layouts.app')

@section('content')

<div class="row">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">Edit Selling Tip</h4>

                    <form id="submit-form" action="{{ route('admin.selling_tips.update', $sellingTip->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') ?? $sellingTip->title }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') ?? $sellingTip->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">Current Image:</label>
                            <br>
                            @if($sellingTip->image)
                                 <img src="{{ asset('public/storage/article_images/' . $sellingTip->image) }}" width='200' height='150' alt="Stock Image">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="image">Change Image:</label>
                            <input type="file" name="image" class="form-control-file">
                        </div>

                        <div class="tile-footer">
                            <button id="submit-btn" class="btn btn-primary" type="submit">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Update Selling Tip
                            </button>
                            <a href="{{ route('admin.selling_tips.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(function() {
        $('#submit-form').ajaxForm({
            beforeSubmit: function() {
                $(".error").remove();
                disable("#submit-btn", true);
                $("body").LoadingOverlay("show");
            },
            error: function(err) {
                $("body").LoadingOverlay("hide");
                handleError(err);
                disable("#submit-btn", false);
            },
            success: function(response) {
                disable("#submit-btn", false);
                $("body").LoadingOverlay("hide");

                if (response.status == 'true') {
                    swal({
                        title: response.message,
                        icon: "success",
                        dangerMode: false,
                    }).then(function(isConfirm) {
                        if (isConfirm) {
                            window.location.href = "{{ route('admin.selling_tips.index') }}";
                        }
                    });

                } else {
                    swal({
                        title: response.message,
                        icon: "error",
                        dangerMode: true,
                    });
                }
            }
        });
    });
</script>
@endsection
