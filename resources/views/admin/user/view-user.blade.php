@extends('admin.layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile p-0">
                {{-- <h4 class="tile-title folder-head">{{ $title }}</h4> --}}
                <div class="tile-body">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">

                                <tbody>
                                    @if ($user->profile_picture)
                                        <tr>
                                            <th>Profile Picture</th>
                                            <td><a href={{$user->profile_picture_url}} target="_blank"><img class="sm-img" src="{{ $user->profile_picture_url }}"></a></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Username</th>
                                        <td>{{ $user->user_name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>

                                    @if($user->country_code)
                                    <tr>
                                        <th>Country Code</th>
                                        <td>+{{ $user->country_code }}</td>
                                    </tr>
                                    @endif

                                    @if($user->mobile)
                                    <tr>
                                        <th>Mobile no.</th>
                                        <td>{{ $user->mobile }}</td>
                                    </tr>
                                    @endif

                                    @if($user->bio)
                                    <tr>
                                        <th>Bio</th>
                                        <td>{{ $user->bio }}</td>
                                    </tr>
                                    @endif

                                    @if($user->address)
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                    @endif

                                    @if($user->dob)
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ $user->dob }}</td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <th>Subscription</th>
                                        <td>{{ $user->is_premium == '1' ? 'Premium' : 'Free' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $user->status? 'Active' : 'Inactive' }}</td>
                                    </tr>


                                    <tr>
                                        <th>Created Date</th>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('m/d/Y') }}</td>
                                    </tr>

                                    <tr>
                                        <th>User Report</th>
                                        <td><a href="{{ url('admin/user/reported-info/' . $user->id) }} "><button class="btn btn-primary btn-md updateData">View Report</button></a></td>
                                    </tr>

                                </tbody>
                            </table>

                            <a href="{{ URL::previous() }}"><button type="button" class="btn btn-primary mt-4">Back</button></a>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '.viewImage', function(event) {
            let image_source = $(this).attr('src');
            let html = `<img src="${image_source}" alt="QR Image">`;
            $("#viewImageBtn .modal-body").html(html);
        });
    </script>
@endsection
