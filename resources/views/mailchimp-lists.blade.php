@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="/mailchim-process-list">

                        @csrf

                        @foreach ($lists as $list)
                            
                            <input type="checkbox" name="list-id" id="list-id" value="{{ $list->id }}" />
                         
                        @endforeach

                        <input id="submit" type="submit" value="Synchronize Now" />

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
