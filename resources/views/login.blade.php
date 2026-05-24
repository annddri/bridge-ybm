<x-header title="Login - Bridge" css="css/login.css"></x-header>

<div class="container d-flex justify-content-center p-3">
    <div class="card login-card p-4 p-md-5 shadow-lg">
        
        <div class="text-center mb-4">
            <img src="{{ asset('img/New Logo YBM Secondary.png') }}" alt="YBM BRILiaN Logo" class="img-fluid mb-3" style="max-height: 55px; object-fit: contain;">
            
            <h3 class="fw-bold fs-6 fs-md-5 px-1" style="color: #063255; line-height: 1.4;">
                BRIDGE <br class="d-md-none"> <br><span class="fw-normal text-secondary fs-7 d-block d-md-inline">(BRIght Dormitory Growth Environment)</span>
            </h3>

            <div class="brand-subtitle mt-2 mb-1" style="color: #2F5CAA;">Bright Scholarship</div>
        </div>
        
        @if (session('error'))
            <div class="alert alert-danger text-center py-2 fs-7 rounded-3">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary fs-7">Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary fs-7">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-custom-primary w-100 fw-bold text-white">Masuk</button>
        </form>
        
        <p class="text-center mt-4 mb-0 text-secondary fs-7">
            Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold" style="color: #0d6efd;">Daftar</a>
        </p>
    </div>
</div>

<x-footer></x-footer>