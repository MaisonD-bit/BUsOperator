<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - North Terminal Bus Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .flex-grow-1 {
            flex: 1;
        }
        .photo-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-bus fa-2x text-primary mb-3"></i>
                            <h3 class="fw-bold text-dark mb-2">North Terminal Bus Operator Registration</h3>
                            <div class="badge bg-success rounded-pill p-2 fs-6">
                                <i class="fas fa-building me-2"></i>Company Affiliation
                            </div>
                        </div>
                        <form method="POST" action="{{ route('register') }}" id="registerForm" enctype="multipart/form-data">
                            @csrf
                            <!-- Basic Information -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Enter your full name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Enter your email"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Create a strong password"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                    </label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirm your password"
                                           required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="contact_number" class="form-label fw-semibold">
                                    <i class="fas fa-phone me-2"></i>Contact Number
                                </label>
                                <input type="text"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       id="contact_number"
                                       name="contact_number"
                                       value="{{ old('contact_number') }}"
                                       placeholder="e.g., +63 912 345 6789"
                                       required>
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Photo Upload -->
                            <div class="mb-4">
                                <label for="photo" class="form-label fw-semibold">
                                    <i class="fas fa-camera me-2"></i>Upload Photo (Company Logo or Operator Photo)
                                </label>
                                <input type="file"
                                       class="form-control @error('photo') is-invalid @enderror"
                                       id="photo"
                                       name="photo"
                                       accept="image/jpeg, image/png, image/jpg"
                                       onchange="previewPhoto(event)">
                                <small class="text-muted">Allowed formats: JPG, JPEG, PNG. Max size: 2MB</small>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <img id="photoPreview" class="photo-preview" alt="Photo preview">
                                </div>
                            </div>
                            <!-- Company Information -->
                            <div class="bg-light p-4 rounded-3 border border-success border-dashed mt-4">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-building me-2"></i>Company Information
                                </h5>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="company_name" class="form-label fw-semibold">Company Name</label>
                                        <input type="text"
                                               class="form-control @error('company_name') is-invalid @enderror"
                                               id="company_name"
                                               name="company_name"
                                               value="{{ old('company_name') }}"
                                               placeholder="Enter your company name"
                                               required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_address" class="form-label fw-semibold">Company Address</label>
                                        <input type="text"
                                               class="form-control @error('company_address') is-invalid @enderror"
                                               id="company_address"
                                               name="company_address"
                                               value="{{ old('company_address') }}"
                                               placeholder="Enter your company address"
                                               required>
                                        @error('company_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="company_contact" class="form-label fw-semibold">Company Contact Number</label>
                                        <input type="text"
                                               class="form-control @error('company_contact') is-invalid @enderror"
                                               id="company_contact"
                                               name="company_contact"
                                               value="{{ old('company_contact') }}"
                                               placeholder="Enter your company contact number"
                                               required>
                                        @error('company_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_email" class="form-label fw-semibold">Company Email</label>
                                        <input type="email"
                                               class="form-control @error('company_email') is-invalid @enderror"
                                               id="company_email"
                                               name="company_email"
                                               value="{{ old('company_email') }}"
                                               placeholder="Enter your company email"
                                               required>
                                        @error('company_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="fleet_size" class="form-label fw-semibold">Fleet Size</label>
                                    <input type="number"
                                           class="form-control @error('fleet_size') is-invalid @enderror"
                                           id="fleet_size"
                                           name="fleet_size"
                                           value="{{ old('fleet_size') }}"
                                           placeholder="Number of buses in your fleet"
                                           required>
                                    @error('fleet_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="routes_served" class="form-label fw-semibold">Routes Served</label>
                                    <textarea class="form-control @error('routes_served') is-invalid @enderror"
                                              id="routes_served"
                                              name="routes_served"
                                              rows="2"
                                              placeholder="List the routes your company serves">{{ old('routes_served') }}</textarea>
                                    @error('routes_served')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Register Button -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Register as Bus Operator
                                </button>
                            </div>
                            <input type="hidden" name="role" value="operator">
                        </form>
                        <!-- Links -->
                        <div class="text-center mt-3">
                            <p class="text-muted mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Login here</a></p>
                        </div>
                        <!-- Terminal Info -->
                        <div class="mt-3 p-3 bg-light rounded-3 text-center small text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            This registration is for <strong>Bus Operators</strong> providing buses and drivers to North Terminal.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewPhoto(event) {
            const preview = document.getElementById('photoPreview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
