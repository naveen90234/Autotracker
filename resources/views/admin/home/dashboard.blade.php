@extends('admin.layouts.app')
@section('content')
    <style>
        .card {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #FFFFFF;
            background-clip: border-box;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%);
            border-radius: 10px;
            border: none;

        }

        .icon-shape {
            border-radius: 0.75rem;
            width: 64px;
            height: 64px;
            margin-top: -2.375rem !important;

        }

        .card .icon-shape i {
            top: 31%;
            color: #FFFFFF;
            font-size: 1.5rem;
            position: relative;
        }

        .text-pramary {
            color: #FFFFFF;
        }

        .text-info {
            color: #FFFFFF;
        }

        categoryPageList a:hover {
            color: #F3133E;
            text-decoration: underline;
        }

        .card-header {
            background-color: #fff !important;
            border-bottom: none;
            margin-bottom: 10px;
            padding: 1rem !important;
        }

        .bg-gradient-success {
            background: #28a745;
        }

        .bg-gradient-danger {
            background: #F3133E;
        }

        .bg-gradient-dark {
            background: linear-gradient(195deg, #42424a, #191919);

        }

        .bg-gradient-primary {
            background: linear-gradient(195deg, #521969, #3D208E);
        }

        .bg-gradient-liteblue {
            background: linear-gradient(195deg, #49a3f1, #1a73e8);

        }

        .bg-success {
            background: #471D7D !important;
        }

        .card h4 {
            font-size: .800rem;
            line-height: 1.375;
            font-weight: 600;
            -webkit-font-smoothing: antialiased;
            font-family: Lato, sans-serif;

        }

        .text-right {
            text-align: right !important;
        }

        .text-capitalize {
            text-transform: capitalize !important;
        }

        .card-header h4 {
            color: black;
        }




        .newCard:hover {
            box-shadow: 0 10px 8px 1px rgb(0 0 0 / 10%), 0 10px 8px -1px rgb(0 0 0 / 6%) !important;
        }

        .newCard a:link {
            text-decoration: none;
            color: black;
        }
    </style>
    <div class="row mt-3">
        <div class="col-xl-12 flex-column d-flex grid-margin stretch-card">
            <div class="row flex-grow">


                {{-- <div class="col-sm-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><a class="text-decoration-none"
                                    href="{{route('admin.users-list')}}">Total Users</a></h4>
                            <p>Total Users</p>
                            <h4 class="text-dark font-weight-bold mb-2">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><a class="text-decoration-none"
                                    href="{{route('admin.users-list')}}">Active Users</a></h4>
                            <p>Active Users</p>
                            <h4 class="text-dark font-weight-bold mb-2">{{ $activeUsers }}</h4>
                        </div>
                    </div>
                </div> --}}

                <div class="col-xl-3 col-sm-6 mb-2">
                    <div class="card newCard">
                        <a href="{{ route('admin.users-list') }}">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-shape bg-success shadow-dark text-center position-absolute">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="text-right pt-1">
                                    <h4 class="text-sm mb-0 text-capitalize">Total Users</h4>
                                    <h4 class="mb-0" style="font-size: 1.5rem;">{{ $totalUsers }} </h4>
                                </div>
                            </div>
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-2">
                    <div class="card newCard">
                        <a href="{{ route('admin.users-list') }}">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-shape bg-gradient-success shadow-dark text-center position-absolute">
                                    <i class="fa fa-envira"></i>
                                </div>
                                <div class="text-right pt-1">
                                    <h4 class="text-sm mb-0 text-capitalize">Active Users</h4>
                                    <h4 class="mb-0" style="font-size: 1.5rem;">{{ $activeUsers }} </h4>
                                </div>
                            </div>
                            <div class="card-footer p-3">
                                <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span></p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
