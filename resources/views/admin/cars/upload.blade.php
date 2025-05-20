@extends('admin.layouts.app')

@section('content')
    <style>
        .upload-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .custom-file {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            border: 2px dashed #007bff;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .custom-file input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
    </style>

    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="upload-container">
                                        <h4 class="mb-4 text-center">Upload Cars via CSV</h4>
                                        <form id="csvUploadForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="custom-file">
                                                <input type="file" name="csv_file" id="csvFile" required>
                                                <span>Click to select a CSV file</span>
                                            </div>
                                            <button type="submit" class="btn btn-primary mt-3 w-100">Upload</button>
                                        </form>
                                        <div id="uploadMessage" class="mt-3 text-center"></div>
                                    <a href="{{ route('admin.cars.index') }}" class="btn btn-secondary mt-1 w-100">Back</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#csvUploadForm').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                
                $.ajax({
                    url: "{{ route('cars.uploadCSV') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#uploadMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.cars.index') }}";
                        }, 2000);
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON.message || "An error occurred.";
                        $('#uploadMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                });
            });
        });
    </script>
@endpush
