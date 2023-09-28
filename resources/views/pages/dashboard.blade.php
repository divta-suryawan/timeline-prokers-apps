@extends('layouts.master')
@section('content')
     <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat Datang Di Dashboard 🎉</h5>

                        @auth
                            <p class="mb-4">{{ auth()->user()->name }}</p>
                        @endauth

                        <i class="fa-sharp fa-solid fa-face-smile text-warning"></i>
                        <a href="javascript:;" class="">Enjoy your work !!!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
