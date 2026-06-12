<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $appTitle }} — Reset Password</title>
  <link rel="shortcut icon" href="{{ asset($appFavicon) }}" />
  <link rel="stylesheet" href="{{ URL::to('/') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    @keyframes fadeIn   { from{opacity:0} to{opacity:1} }
    @keyframes slideUp  { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
    @keyframes slideInL { from{opacity:0;transform:translateX(-40px)} to{opacity:1;transform:translateX(0)} }
    @keyframes slideInR { from{opacity:0;transform:translateX(40px)}  to{opacity:1;transform:translateX(0)} }
    @keyframes pulseCross {
      0%,100%{ opacity:.04; transform:scale(1) rotate(0deg); }
      50%    { opacity:.08; transform:scale(1.04) rotate(3deg); }
    }
    @keyframes floatUp {
      0%,100%{ transform:translateY(0); }
      50%    { transform:translateY(-8px); }
    }
    @keyframes shimmer {
      0%  { background-position:-400% center; }
      100%{ background-position:400% center; }
    }
    @keyframes inputGlow {
      0%,100%{ box-shadow:0 0 0 3px rgba(1,48,84,.08); }
      50%    { box-shadow:0 0 0 3px rgba(1,48,84,.18); }
    }
    @keyframes spin {
      from{ transform:rotate(0deg); }
      to  { transform:rotate(360deg); }
    }

    html,body { height:100%; }

    body {
      display:flex; align-items:stretch;
      font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
      background:#f0f4f8;
      overflow:hidden;
      animation:fadeIn .5s ease both;
    }

    /* ═══════════════ LEFT PANEL ═══════════════ */
    .left {
      width:48%;
      background:linear-gradient(160deg,#013054 0%,#012040 60%,#010f1e 100%);
      position:relative; overflow:hidden;
      display:flex; flex-direction:column;
      justify-content:center; align-items:center;
      padding:50px 48px 40px;
      animation:slideInL .85s cubic-bezier(.22,.61,.36,1) both;
      text-align:center;
    }

    .cross-bg {
      position:absolute; inset:0;
      background-image:
        linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);
      background-size:44px 44px;
      pointer-events:none;
    }
    .cross-mark {
      position:absolute;
      font-size:160px; font-weight:900; color:#fff;
      opacity:.04; user-select:none;
      animation:pulseCross 8s ease-in-out infinite;
    }
    .cross-mark.c1{ top:-30px; right:-20px; }
    .cross-mark.c2{ bottom:60px; left:-30px; font-size:120px; animation-delay:3s; }

    .left-inner { position:relative; z-index:2; display:flex; flex-direction:column; align-items:center; }

    .logo-box {
      width:140px; height:140px;
      background:rgba(255,255,255,.1);
      border:1px solid rgba(255,255,255,.2);
      border-radius:22px;
      display:flex; align-items:center; justify-content:center;
      padding:16px; margin-bottom:28px;
      animation:floatUp 4s ease-in-out infinite;
    }
    .logo-box img{ width:100%; height:100%; object-fit:contain; }

    .brand-name {
      color:#fff; font-size:26px; font-weight:800;
      letter-spacing:.4px; margin-bottom:10px;
    }
    .brand-tag {
      color:rgba(255,255,255,.5); font-size:13px; line-height:1.7;
      max-width:300px; margin-bottom:40px;
    }

    /* Lock icon circle */
    .lock-circle {
      width:80px; height:80px;
      background:rgba(79,195,247,.12);
      border:2px solid rgba(79,195,247,.3);
      border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      margin-bottom:16px;
    }
    .lock-circle i { color:#4FC3F7; font-size:32px; }

    .security-title {
      color:#fff; font-size:18px; font-weight:700;
      margin-bottom:8px;
    }
    .security-desc {
      color:rgba(255,255,255,.4); font-size:12.5px; line-height:1.7;
      max-width:260px;
    }

    /* Steps */
    .steps {
      margin-top:32px; display:flex; flex-direction:column; gap:14px;
      width:100%; max-width:280px;
    }
    .step {
      display:flex; align-items:center; gap:14px;
      background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.1);
      border-radius:12px; padding:12px 16px;
    }
    .step-num {
      width:26px; height:26px; border-radius:50%;
      background:rgba(79,195,247,.2);
      border:1.5px solid #4FC3F7;
      color:#4FC3F7; font-size:12px; font-weight:800;
      display:flex; align-items:center; justify-content:center;
      flex-shrink:0;
    }
    .step-text { color:rgba(255,255,255,.6); font-size:12px; line-height:1.5; text-align:left; }

    .left-foot {
      position:absolute; bottom:24px;
      color:rgba(255,255,255,.2); font-size:11px;
      z-index:2;
    }

    /* ═══════════════ RIGHT PANEL ═══════════════ */
    .right {
      width:52%; background:#fff;
      display:flex; align-items:center; justify-content:center;
      padding:60px 70px;
      animation:slideInR .85s cubic-bezier(.22,.61,.36,1) both;
      overflow-y:auto;
    }

    .form-wrap { width:100%; max-width:380px; }

    /* Back link */
    .back-link {
      display:inline-flex; align-items:center; gap:6px;
      color:#94a3b8; font-size:13px; text-decoration:none;
      margin-bottom:28px;
      transition:color .2s;
      animation:slideUp .5s .1s both;
    }
    .back-link:hover{ color:#013054; }
    .back-link i{ font-size:16px; }

    .form-title {
      font-size:28px; font-weight:800;
      color:#012040; margin-bottom:4px;
      animation:slideUp .6s .2s both;
    }
    .form-sub {
      color:#94a3b8; font-size:13.5px;
      margin-bottom:28px; line-height:1.6;
      animation:slideUp .6s .3s both;
    }

    /* Alerts */
    .alert{
      padding:12px 16px; border-radius:10px;
      margin-bottom:20px; font-size:13px; border-left:4px solid;
      display:flex; align-items:flex-start; gap:10px;
      animation:slideUp .4s both;
    }
    .alert i{ font-size:16px; flex-shrink:0; margin-top:1px; }
    .alert-success{ background:#f0fdf4; color:#166534; border-color:#22c55e; }
    .alert-success i{ color:#22c55e; }
    .alert-danger { background:#fef2f2; color:#991b1b; border-color:#ef4444; }
    .alert-danger i{ color:#ef4444; }

    /* Field */
    .field { margin-bottom:20px; animation:slideUp .6s .4s both; }

    .field label {
      display:block; font-size:11.5px; font-weight:700;
      color:#64748b; text-transform:uppercase;
      letter-spacing:.8px; margin-bottom:8px;
    }
    .input-box { position:relative; }
    .input-box i {
      position:absolute; left:16px; top:50%;
      transform:translateY(-50%);
      color:#94a3b8; font-size:17px;
      transition:color .25s; pointer-events:none;
    }
    .input-box input {
      width:100%; padding:13px 16px 13px 46px;
      border:2px solid #e2e8f0; border-radius:12px;
      font-size:14px; color:#1e293b;
      background:#f8fafc; outline:none;
      transition:all .3s ease;
    }
    .input-box input::placeholder{ color:#cbd5e1; }
    .input-box input:focus{
      border-color:#013054; background:#fff;
      animation:inputGlow 2s ease infinite;
    }
    .input-box input:focus + i,
    .input-box input:focus ~ i{ color:#013054; }
    .err{ color:#ef4444; font-size:11.5px; margin-top:5px; display:block; }

    /* Submit */
    .btn-submit{
      width:100%; padding:14.5px;
      border:none; border-radius:12px;
      font-size:14px; font-weight:700;
      letter-spacing:1.2px; text-transform:uppercase;
      cursor:pointer; color:#fff;
      background:linear-gradient(90deg,#013054,#025a9e,#0288d1,#025a9e,#013054);
      background-size:400% auto;
      animation:slideUp .6s .5s both, shimmer 5s 1.2s linear infinite;
      transition:transform .2s,box-shadow .3s;
      margin-top:4px;
    }
    .btn-submit:hover{
      transform:translateY(-2px);
      box-shadow:0 12px 32px rgba(1,48,84,.35);
    }
    .btn-submit:active{ transform:translateY(0); }

    /* Trust */
    .trust{
      display:flex; justify-content:center; gap:20px;
      margin-top:24px;
      animation:slideUp .6s .6s both;
    }
    .badge{
      display:flex; align-items:center; gap:6px;
      color:#94a3b8; font-size:11.5px;
    }
    .badge i{ font-size:14px; color:#013054; }

    .divider {
      display:flex; align-items:center; gap:12px;
      margin:20px 0 16px;
      animation:slideUp .6s .55s both;
    }
    .divider hr{ flex:1; border:none; border-top:1px solid #e2e8f0; }
    .divider span{ color:#cbd5e1; font-size:12px; }

    .login-link {
      display:block; text-align:center;
      color:#013054; font-size:13.5px; font-weight:700;
      text-decoration:none; padding:12px;
      border:2px solid #e2e8f0; border-radius:12px;
      transition:all .25s;
      animation:slideUp .6s .6s both;
    }
    .login-link:hover{
      background:#f8fafc; border-color:#013054;
      box-shadow:0 4px 16px rgba(1,48,84,.1);
    }

    @media(max-width:860px){
      body{ flex-direction:column; overflow:auto; }
      .left,.right{ width:100%; }
      .left{ padding:40px 32px 50px; }
      .right{ padding:40px 32px; }
    }
  </style>
</head>
<body>

<!-- ─── LEFT PANEL ─── -->
<div class="left">
  <div class="cross-bg"></div>
  <div class="cross-mark c1">✚</div>
  <div class="cross-mark c2">✚</div>

  <div class="left-inner">
    <div class="logo-box">
      <img src="{{ asset($appLogo) }}" alt="{{ $appTitle }}">
    </div>
    <div class="brand-name">{{ $appTitle }}</div>
    <div class="brand-tag">Secure account recovery for healthcare professionals.</div>

    <div class="lock-circle">
      <i class="mdi mdi-lock-reset"></i>
    </div>
    <div class="security-title">Account Recovery</div>
    <div class="security-desc">We take your account security seriously. Follow the steps to regain access safely.</div>

    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-text">Enter your registered email address</div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-text">Check your inbox for the reset link</div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-text">Create a new secure password</div>
      </div>
    </div>
  </div>

  <div class="left-foot">&copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.</div>
</div>

<!-- ─── RIGHT PANEL ─── -->
<div class="right">
  <div class="form-wrap">

    <a href="{{ route('login') }}" class="back-link">
      <i class="mdi mdi-arrow-left"></i> Back to Sign In
    </a>

    <div class="form-title">Reset Password</div>
    <div class="form-sub">Enter your email and we'll send you a secure link to reset your password.</div>

    @if(session('status'))
      <div class="alert alert-success">
        <i class="mdi mdi-check-circle"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if($errors->has('email'))
      @if($errors->has('status'))
        <div class="alert alert-danger">
          <i class="mdi mdi-alert-circle"></i>
          <span>{{ $errors->first('email') }}</span>
        </div>
      @else
        <div class="alert alert-success">
          <i class="mdi mdi-check-circle"></i>
          <span>We have e-mailed your password reset link!</span>
        </div>
      @endif
    @endif

    <form method="POST" id="forgotPasswordForm" action="{{ route('password.email') }}">
      {{ csrf_field() }}

      <div class="field">
        <label>Email Address</label>
        <div class="input-box">
          <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="doctor@hospital.com" autocomplete="email">
          <i class="mdi mdi-email-outline"></i>
        </div>
        <span id="email-err" class="err"></span>
      </div>

      <button type="submit" class="btn-submit">
        <i class="mdi mdi-send"></i>&nbsp; Send Reset Link
      </button>
    </form>

    <div class="divider">
      <hr><span>or</span><hr>
    </div>

    <a href="{{ route('login') }}" class="login-link">
      <i class="mdi mdi-login"></i> Return to Sign In
    </a>

    <div class="trust">
      <div class="badge"><i class="mdi mdi-shield-check"></i> Secure</div>
      <div class="badge"><i class="mdi mdi-lock"></i> Encrypted</div>
      <div class="badge"><i class="mdi mdi-eye-off"></i> Private</div>
    </div>

  </div>
</div>

<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
  var emailErr = document.getElementById('email-err');
  var email = document.getElementById('email').value.trim();
  var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  emailErr.innerHTML = '';
  if (!email) {
    emailErr.innerHTML = 'Please enter your email address.';
    e.preventDefault(); return;
  }
  if (!regex.test(email)) {
    emailErr.innerHTML = 'Please enter a valid email address.';
    e.preventDefault();
  }
});
</script>
</body>
</html>
