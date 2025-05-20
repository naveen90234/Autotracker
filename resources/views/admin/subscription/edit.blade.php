@extends('admin.layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">

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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ url('admin/subscription/update/' . $subscription->id) }}" class="forms-sample"
                            accept-charset="UTF-8" id="submit-form" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" value="{{ old('name') ?? $subscription->name }}"
                                    id="name" placeholder="Name" name="name">
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="description" class="form-control" id="description" placeholder="description" value="{{ old('description') ?? $subscription->description }}" name="description">
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a href="{{ route('admin.subscription') }}"><button type="button"
                                    class="btn btn-light">Cancel</button></a>
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
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "success",
                            dangerMode: false,
                        }).then(function(isConfirm) {
                            if (isConfirm) {
                                window.location.href = "{{ route('admin.subscription') }}";
                            }
                        });

                    } else {
                        $('#turn-up-error').html('');
                        swal({
                            title: response.message,
                            icon: "error",
                            dangerMode: true,
                        }).then(function(isConfirm) {
                            if (isConfirm) {}
                        });
                    }
                }
            });
        });
    </script>
@endsection
