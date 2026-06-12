@include('include/header')
@include('include/sidebar')

<link href="{{ asset('/assets/css/toastr/toastr.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/css/global.css')}}">

<style>
    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .logo-preview {
        height: 60px;
        max-width: 150px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 4px;
        display: block;
        margin-top: 6px;
    }
    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .color-input-wrap input[type=color] {
        width: 44px;
        height: 38px;
        padding: 2px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<div class="main-panel main-page-box">
    <div class="content-wrapper content-wrapper-box">

        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">{{ $config ? 'Edit Company Master' : 'Add Company Master' }}</h5>
            <a href="{{ route('domain-config.index') }}" class="btn btn-secondary btn-sm px-4">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>
        <hr />

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ $config ? route('domain-config.update', $config->id) : route('domain-config.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if($config)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Domain -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Domain <span class="text-danger">*</span></label>
                            <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror"
                                value="{{ old('domain', $config->domain ?? '') }}"
                                placeholder="e.g. nybestmedical.test">
                            @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Company Name -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Company Name</label>
                            <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                value="{{ old('company_name', $config->company_name ?? '') }}"
                                placeholder="e.g. NY Best Medical">
                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">App Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $config->title ?? '') }}"
                                placeholder="e.g. NY BEST MEDICAL">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Theme Color -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Theme Color <span class="text-danger">*</span></label>
                            <div class="color-input-wrap">
                                <input type="color" id="theme_color_picker"
                                    value="{{ old('theme_color', $config->theme_color ?? '#0F0D0B') }}"
                                    onchange="document.getElementById('theme_color').value=this.value">
                                <input type="text" id="theme_color" name="theme_color"
                                    class="form-control @error('theme_color') is-invalid @enderror"
                                    value="{{ old('theme_color', $config->theme_color ?? '#0F0D0B') }}"
                                    placeholder="#0F0D0B" maxlength="20"
                                    oninput="syncColor('theme_color_picker',this.value)">
                            </div>
                            @error('theme_color')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <!-- Login BG -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Login Background Color <span class="text-danger">*</span></label>
                            <div class="color-input-wrap">
                                <input type="color" id="login_bg_picker"
                                    value="{{ old('login_bg', $config->login_bg ?? '#0F0D0B') }}"
                                    onchange="document.getElementById('login_bg').value=this.value">
                                <input type="text" id="login_bg" name="login_bg"
                                    class="form-control @error('login_bg') is-invalid @enderror"
                                    value="{{ old('login_bg', $config->login_bg ?? '#0F0D0B') }}"
                                    placeholder="#0F0D0B" maxlength="20"
                                    oninput="syncColor('login_bg_picker',this.value)">
                            </div>
                            @error('login_bg')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Logo</label>
                            @if($config && $config->logo)
                                <div class="mb-1">
                                    <img src="{{ asset($config->logo) }}" class="logo-preview" id="logo_preview" onerror="this.style.display='none'">
                                </div>
                                <input type="hidden" name="logo" value="{{ $config->logo }}">
                            @endif
                            <input type="file" name="logo_file" class="form-control-file"
                                accept="image/*" onchange="previewImage(this,'logo_preview')">
                            <small class="text-muted">Upload new logo image (PNG, JPG)</small>
                            @if(!$config || !$config->logo)
                                <img id="logo_preview" class="logo-preview" style="display:none;">
                            @endif
                        </div>

                        <!-- Favicon -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Favicon</label>
                            @if($config && $config->favicon)
                                <div class="mb-1">
                                    <img src="{{ asset($config->favicon) }}" class="logo-preview" id="favicon_preview" style="height:32px;" onerror="this.style.display='none'">
                                </div>
                                <input type="hidden" name="favicon" value="{{ $config->favicon }}">
                            @endif
                            <input type="file" name="favicon_file" class="form-control-file"
                                accept="image/*" onchange="previewImage(this,'favicon_preview')">
                            <small class="text-muted">Upload new favicon (PNG, ICO)</small>
                            @if(!$config || !$config->favicon)
                                <img id="favicon_preview" class="logo-preview" style="display:none;height:32px;">
                            @endif
                        </div>

                        <!-- Logo Style -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Logo CSS Style</label>
                            <input type="text" name="logo_style" class="form-control"
                                value="{{ old('logo_style', $config->logo_style ?? 'width:100%;') }}"
                                placeholder="width:100%;border-radius:25px;">
                            <small class="text-muted">Inline CSS applied to logo img tag</small>
                        </div>

                        <!-- Login Image -->
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-semibold">Login Page Image Path</label>
                            <input type="text" name="login_image" class="form-control"
                                value="{{ old('login_image', $config->login_image ?? 'img/pana.png') }}"
                                placeholder="img/pana.png">
                            <small class="text-muted">Relative path from public folder</small>
                        </div>
                    </div>

                    <hr>
                    <div class="text-right">
                        <a href="{{ route('domain-config.index') }}" class="btn btn-secondary px-4 mr-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-save mr-1"></i> {{ $config ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function syncColor(pickerId, value) {
    if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
        document.getElementById(pickerId).value = value;
    }
}
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@include('include/footer')
