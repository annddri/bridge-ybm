<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bright Scholarship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Menggunakan warna background gradient Navy khas YBM */
        body { 
            background: linear-gradient(135deg, #063255 0%, #0a426e 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Card dengan Efek Transparansi & Blur Premium (Glassmorphism) */
        .login-card { 
            max-width: 420px; 
            width: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 244, 248, 0.85) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px); /* Dukungan untuk Safari */
            border-radius: 24px; 
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* Styling khusus untuk teks 'BRIGHT Scholarship' */
        .brand-subtitle {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Kustomisasi Input Form */
        .form-control {
            border-radius: 12px;
            padding: 11px 15px;
            border: 1px solid #dee2e6;
            background-color: rgba(248, 249, 250, 0.8);
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        /* Kustomisasi Tombol Masuk */
        .btn-custom-primary {
            background-color: #0d6efd;
            border: none;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.2s ease;
        }
        .btn-custom-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3);
        }
        .btn-custom-primary:active {
            transform: translateY(1px);
        }

        /* Ukuran teks alert diperkecil sedikit agar fit */
        .fs-7 { font-size: 0.875rem; }
    </style>
</head>
<body>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>