<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $appTitle }}</title>
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
    @keyframes ecgDraw {
      from{ stroke-dashoffset: 900; }
      to  { stroke-dashoffset: 0; }
    }
    @keyframes ecgLoop {
      0%  { stroke-dashoffset: 0; opacity:1; }
      80% { stroke-dashoffset:-900; opacity:1; }
      81% { opacity:0; }
      82% { stroke-dashoffset:900; opacity:0; }
      100%{ stroke-dashoffset:900; opacity:0; }
    }
    @keyframes heartbeat {
      0%,100%{ transform:scale(1); }
      14%    { transform:scale(1.2); }
      28%    { transform:scale(1); }
      42%    { transform:scale(1.15); }
      56%    { transform:scale(1); }
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
      justify-content:space-between;
      padding:50px 48px 40px;
      animation:slideInL .85s cubic-bezier(.22,.61,.36,1) both;
    }

    /* Medical cross watermark pattern */
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
    .cross-mark.c1{ top:-30px; right:-20px; animation-delay:0s; }
    .cross-mark.c2{ bottom:60px; left:-30px; font-size:120px; animation-delay:3s; }

    /* Top: logo + brand */
    .left-top { position:relative; z-index:2; display:flex; flex-direction:column; align-items:center; text-align:center; margin-top:150px; }

    .logo-box {
      width:140px; height:140px;
      background:rgba(255,255,255,.1);
      border:1px solid rgba(255,255,255,.2);
      border-radius:22px;
      display:flex; align-items:center; justify-content:center;
      padding:16px; margin-bottom:24px;
      animation:floatUp 4s ease-in-out infinite;
      transition:transform .3s,box-shadow .3s;
    }
    .logo-box:hover{
      transform:scale(1.05) translateY(-4px);
      box-shadow:0 16px 40px rgba(79,195,247,.25);
    }
    .logo-box img{ width:100%; height:100%; object-fit:contain; }

    .brand-name {
      color:#fff; font-size:26px; font-weight:800;
      letter-spacing:.4px; line-height:1.2;
      margin-bottom:10px;
    }
    .brand-tag {
      color:rgba(255,255,255,.5); font-size:13px; line-height:1.6;
      max-width:300px;
    }

    /* Stats row */
    .stats {
      position:relative; z-index:2;
      display:flex; gap:24px; margin-top:36px;
    }
    .stat-card {
      flex:1; background:rgba(255,255,255,.07);
      border:1px solid rgba(255,255,255,.1);
      border-radius:14px; padding:14px 16px;
      transition:background .3s;
    }
    .stat-card:hover{ background:rgba(255,255,255,.12); }
    .stat-num {
      color:#4FC3F7; font-size:20px; font-weight:800;
      letter-spacing:.5px;
    }
    .stat-lbl {
      color:rgba(255,255,255,.45); font-size:11px;
      margin-top:2px; text-transform:uppercase; letter-spacing:.5px;
    }

    /* ECG / heartbeat section */
    .ecg-section {
      position:relative; z-index:2;
      margin-top:32px;
    }
    .ecg-label {
      display:flex; align-items:center; gap:8px;
      color:rgba(255,255,255,.4); font-size:11px;
      text-transform:uppercase; letter-spacing:.8px;
      margin-bottom:8px;
    }
    .heart-icon {
      color:#ef5350; font-size:14px;
      animation:heartbeat 1.4s ease-in-out infinite;
    }
    .ecg-wrap {
      background:rgba(0,0,0,.25); border-radius:10px;
      padding:10px 12px; overflow:hidden;
    }
    svg.ecg { width:100%; height:48px; display:block; }
    .ecg-line {
      fill:none; stroke:#4FC3F7; stroke-width:2;
      stroke-dasharray:900;
      animation:ecgDraw 2.5s ease forwards,
                ecgLoop 4s 2.5s linear infinite;
    }

    /* Bottom copyright */
    .left-foot {
      position:relative; z-index:2;
      color:rgba(255,255,255,.25); font-size:11px;
      margin-top:28px;
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

    .form-title {
      font-size:28px; font-weight:800;
      color:#012040; margin-bottom:4px;
      animation:slideUp .6s .3s both;
    }
    .form-sub {
      color:#94a3b8; font-size:13.5px;
      margin-bottom:32px;
      animation:slideUp .6s .4s both;
    }
    .form-sub span{ color:#013054; font-weight:700; }

    /* Alerts */
    .alert{
      padding:11px 15px; border-radius:10px;
      margin-bottom:20px; font-size:13px; border-left:4px solid;
    }
    .alert-success{ background:#f0fdf4; color:#166534; border-color:#22c55e; }
    .alert-danger { background:#fef2f2; color:#991b1b; border-color:#ef4444; }

    /* Field */
    .field { margin-bottom:20px; animation:slideUp .6s both; }
    .field:nth-child(1){ animation-delay:.45s; }
    .field:nth-child(2){ animation-delay:.55s; }

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
      border-color:#013054;
      background:#fff;
      animation:inputGlow 2s ease infinite;
    }
    .input-box input:focus ~ i{ color:#013054; }
    .err{ color:#ef4444; font-size:11.5px; margin-top:5px; display:block; }

    /* Footer controls */
    .ctrl-row{
      display:flex; justify-content:space-between;
      align-items:center; margin:4px 0 24px;
      animation:slideUp .6s .65s both;
    }
    .chk-label{
      display:flex; align-items:center; gap:8px;
      color:#64748b; font-size:13px; cursor:pointer;
    }
    .chk-label input{ accent-color:#013054; width:15px; height:15px; }
    .forgot-lnk{
      color:#013054; font-size:13px; font-weight:700;
      text-decoration:none; transition:color .2s;
    }
    .forgot-lnk:hover{ color:#4FC3F7; }

    /* Submit */
    .btn-submit{
      width:100%; padding:14.5px;
      border:none; border-radius:12px;
      font-size:14px; font-weight:700;
      letter-spacing:1.2px; text-transform:uppercase;
      cursor:pointer; color:#fff;
      background:linear-gradient(90deg,#013054,#025a9e,#0288d1,#025a9e,#013054);
      background-size:400% auto;
      animation:slideUp .6s .75s both, shimmer 5s 1.4s linear infinite;
      transition:transform .2s ease,box-shadow .3s ease;
      position:relative; overflow:hidden;
    }
    .btn-submit:hover{
      transform:translateY(-2px);
      box-shadow:0 12px 32px rgba(1,48,84,.35);
    }
    .btn-submit:active{ transform:translateY(0); }

    /* Trust badges */
    .trust{
      display:flex; justify-content:center; gap:20px;
      margin-top:24px;
      animation:slideUp .6s .85s both;
    }
    .badge{
      display:flex; align-items:center; gap:6px;
      color:#94a3b8; font-size:11.5px;
    }
    .badge i{ font-size:14px; color:#013054; }

    .links-row{
      display:flex; justify-content:space-between;
      margin-top:20px;
      animation:slideUp .6s .9s both;
    }
    .links-row a{
      color:#64748b; font-size:11.5px;
      text-decoration:none; transition:color .2s;
    }
    .links-row a:hover{ color:#013054; }

    /* Responsive */
    @media(max-width:860px){
      body{ flex-direction:column; overflow:auto; }
      .left,.right{ width:100%; }
      .left{ padding:40px 32px 36px; }
      .right{ padding:40px 32px; }
      .stats{ gap:12px; }
    }
  </style>
</head>
<body>

<!-- ─── LEFT PANEL ─── -->
<div class="left">
  <div class="cross-bg"></div>
  <div class="cross-mark c1">✚</div>
  <div class="cross-mark c2">✚</div>

  <div class="left-top">
    <div class="logo-box">
      <img src="{{ asset($appLogo) }}" alt="{{ $appTitle }}">
    </div>
    <div class="brand-name">{{ $appTitle }}</div>
    <div class="brand-name">HEALTH GROUP PC</div>
    <div class="brand-tag">Empowering healthcare professionals with secure, intelligent management solutions.</div>

    <!-- <div class="stats">
      <div class="stat-card">
        <div class="stat-num">99.9%</div>
        <div class="stat-lbl">Uptime</div>
      </div>
      <div class="stat-card">
        <div class="stat-num">256-bit</div>
        <div class="stat-lbl">Encryption</div>
      </div>
      <div class="stat-card">
        <div class="stat-num">HIPAA</div>
        <div class="stat-lbl">Compliant</div>
      </div>
    </div> -->
  </div>

  <div class="ecg-section">
    <!-- <div class="ecg-label">
      <i class="mdi mdi-heart heart-icon"></i>
      Live System Status — All Systems Operational
    </div> -->
    <!-- <div class="ecg-wrap">
      <svg class="ecg" viewBox="0 0 900 48" preserveAspectRatio="none">
        <polyline class="ecg-line"
          points="0,24 60,24 80,24 95,4 110,44 125,24 145,24
                  205,24 225,24 240,4 255,44 270,24 290,24
                  350,24 370,24 385,4 400,44 415,24 435,24
                  495,24 515,24 530,4 545,44 560,24 580,24
                  640,24 660,24 675,4 690,44 705,24 725,24
                  785,24 805,24 820,4 835,44 850,24 900,24"/>
      </svg>
    </div> -->
  </div>

  <div class="left-foot">
    &copy; {{ date('Y') }} {{ $appTitle }}. All rights reserved.
  </div>
</div>

<!-- ─── RIGHT PANEL ─── -->
<div class="right">
  <div class="form-wrap">
    <div class="form-title">Welcome Back</div>
    <div class="form-sub">Sign in to <span>{{ $appTitle }}</span></div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" id="loginform" action="{{ route('login') }}" onsubmit="return doVal()">
      {{ csrf_field() }}

      <div class="field">
        <label>Email Address</label>
        <div class="input-box">
          <input type="text" id="email" name="email" value="{{ old('email') }}" placeholder="doctor@hospital.com" autocomplete="email">
          <i class="mdi mdi-email-outline"></i>
        </div>
        <span id="email-err" class="err"></span>
        @if($errors->has('email'))
          <span class="err">{{ $errors->first('email') }}</span>
        @endif
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-box">
          <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
          <i class="mdi mdi-lock-outline"></i>
        </div>
        <span id="pass-err" class="err"></span>
        @if($errors->has('password'))
          <span class="err">{{ $errors->first('password') }}</span>
        @endif
      </div>

      <div class="ctrl-row">
        <label class="chk-label">
          <input type="checkbox"> Keep me signed in
        </label>
        <a href="{{ route('password.request') }}" class="forgot-lnk">Forgot password?</a>
      </div>

      <button type="submit" class="btn-submit">
        <i class="mdi mdi-login"></i>&nbsp; Sign In
      </button>
    </form>

    <div class="trust">
      <div class="badge"><i class="mdi mdi-shield-check"></i> Secure Login</div>
      <div class="badge"><i class="mdi mdi-lock"></i> HIPAA Compliant</div>
      <div class="badge"><i class="mdi mdi-eye-off"></i> Private</div>
    </div>

    <div class="links-row">
      <a target="_blank" href="{{ url('/term-condition') }}">Terms &amp; Conditions</a>
      <a target="_blank" href="{{ url('/privacy-policy') }}">Privacy Policy</a>
    </div>
  </div>
</div>

<script>
function doVal(){
  document.getElementById('email-err').innerHTML='';
  document.getElementById('pass-err').innerHTML='';
  var ok=true;
  if(!document.getElementById('email').value.trim()){
    document.getElementById('email-err').innerHTML='Please enter your email.'; ok=false;
  }
  if(!document.getElementById('password').value){
    document.getElementById('pass-err').innerHTML='Please enter your password.'; ok=false;
  }
  return ok;
}
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9EPHQQ3SF5"></script>
<script>
  window.dataLayer=window.dataLayer||[];
  function gtag(){dataLayer.push(arguments);}
  gtag('js',new Date()); gtag('config','G-9EPHQQ3SF5');
</script>
</body>
</html>
