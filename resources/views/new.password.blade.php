<!-- @extends('layout')
@section('content')
<main>
    <div class="ms-auto me-auto mt-5" style="width: 500px">
        <div class="mt-5">
            @if(errors->any())
            <div class="col-12">
                @foreach($errors->all() as $error)
                <div class="alert alert-danger">
                    {{$error}}
                </div>
                @endforeach
            </div>
            @endif

            @if(session()->has('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
            @endif
        </div>
        <form action="{{route('reset.password.post')}}" method="POST">
            @csrf
            <input type="text" name="token" hidden value="{{$token}}">
            <div class=" mb-3">
                <label class="form-label">E-mail Address</label>
                <input type="email" class="form-control" name="email">
            </div>
            <div class="mb-3">
                <label class="form-label">Enter new password</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" class="form-control" name="password_confirmation">
            </div>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>
    </div>
</main>
@endsection -->