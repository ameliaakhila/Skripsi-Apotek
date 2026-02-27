<x-head />
<div class="authincation  d-flex justify-content-center align-items-center h-100">
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-6 ">
                <div class="authincation-content rounded">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form">
                                <div class="d-flex justify-content-center mb-3">
                                    <img src="./assets/images/logosf2.png" style="max-width: 250px;"
                                        class="img-fluid mx-auto d-block">
                                </div>
                                <form method="POST" action="{{ url('/login-process') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="mb-1"><strong>Username</strong></label>
                                        <input type="text" name="username" placeholder="Inputkan Username"
                                            class="form-control @error('username') is-invalid @enderror"
                                            value="{{ old('username') }}">
                                        @error('username')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1"><strong>Password</strong></label>
                                        <input type="password" name="password" placeholder="Inputkan password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            value="{{ old('password') }}">
                                        @error('password')
                                            <div class="text-danger mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary w-100">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>