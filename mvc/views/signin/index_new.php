<style>
/* ============================================================
   Modern Login Page — overrides AdminLTE signin defaults
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; }

html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    background: #fff !important;
    overflow-x: hidden;
}

/* ---------- Wrapper ---------- */
.lp-wrap {
    display: flex;
    min-height: 100vh;
}

/* ---------- LEFT HERO PANEL ---------- */
.lp-hero {
    width: 50%;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.lp-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('<?=base_url('uploads/images/login_left_image.png')?>');
    background-size: cover;
    background-position: center center;
}

/* ---------- RIGHT FORM PANEL ---------- */
.lp-panel {
    width: 50%;
    flex-shrink: 0;
    min-height: 100vh;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 52px 48px;
    position: relative;
    z-index: 2;
    border-left: 2px solid #e4e9f2;
    box-shadow: -6px 0 30px rgba(0,0,0,0.06);
}

.lp-form-inner { width: 100%; }

/* School branding block */
.lp-brand {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 40px;
    padding-bottom: 28px;
    border-bottom: 1.5px solid #f0f3f8;
}

.lp-brand-logo {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    object-fit: cover;
    box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    border: 1px solid #e8eef5;
    flex-shrink: 0;
    background: #f4f6fb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0aec0;
    font-size: 20px;
}

.lp-brand-logo img {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    object-fit: cover;
}

.lp-brand-name {
    font-size: 14px;
    font-weight: 700;
    color: #1a2340;
    margin: 0 0 3px;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 270px;
}

.lp-brand-tag {
    font-size: 11.5px;
    color: #9aa5b8;
    margin: 0;
    font-weight: 400;
}

/* Page heading */
.lp-heading { font-size: 26px; font-weight: 800; color: #1a2340; margin: 0 0 6px; letter-spacing: -0.4px; }
.lp-sub     { font-size: 14px; color: #8e9bae; margin: 0 0 30px; }

/* Alert banners */
.lp-banner {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 15px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 22px;
    position: relative;
}
.lp-banner.err  { background: #fff5f5; border: 1px solid #fecaca; color: #b91c1c; }
.lp-banner.succ { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.lp-banner i { margin-top: 1px; flex-shrink: 0; }
.lp-banner-close {
    position: absolute; right: 12px; top: 10px;
    background: none; border: none; font-size: 15px;
    color: inherit; opacity: 0.6; cursor: pointer; padding: 0; line-height: 1;
}
.lp-banner-close:hover { opacity: 1; }

/* Form field */
.lp-field { margin-bottom: 20px; }
.lp-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #3d4d67;
    margin-bottom: 7px;
    letter-spacing: 0.15px;
    text-transform: uppercase;
}
.lp-field-wrap { position: relative; }

.lp-input {
    width: 100%;
    height: 50px;
    background: #f7f9fc;
    border: 1.5px solid #e4e9f2;
    border-radius: 10px;
    padding: 0 50px 0 16px;
    font-size: 14px;
    color: #1a2340;
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
    appearance: none;
    -webkit-appearance: none;
    font-family: inherit;
}

.lp-input:focus {
    background: #fff;
    border-color: #4361ee;
    box-shadow: 0 0 0 3.5px rgba(67,97,238,.1);
}

.lp-input::placeholder { color: #b8c4d4; font-size: 13.5px; }

.lp-field-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #b8c4d4;
    font-size: 15px;
    cursor: default;
    pointer-events: none;
    transition: color .2s;
}

.lp-pwd-toggle {
    cursor: pointer;
    pointer-events: all;
    user-select: none;
}
.lp-pwd-toggle:hover { color: #4361ee; }

/* Checkbox + Forgot row */
.lp-extras {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    margin-top: 4px;
}

.lp-check {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 13px;
    color: #5a6a85;
    font-weight: 500;
    user-select: none;
}

.lp-check input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #4361ee;
    cursor: pointer;
    flex-shrink: 0;
    border-radius: 4px;
}

.lp-forgot {
    font-size: 13px;
    font-weight: 600;
    color: #4361ee;
    text-decoration: none;
}
.lp-forgot:hover { color: #2f4ac5; text-decoration: underline; }

/* Submit button */
.lp-submit {
    width: 100%;
    height: 52px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #4361ee 0%, #3251d4 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    cursor: pointer;
    box-shadow: 0 4px 18px rgba(67,97,238,.35);
    transition: transform .18s ease, box-shadow .18s ease;
    display: block;
    font-family: inherit;
}
.lp-submit:hover  { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(67,97,238,.45); }
.lp-submit:active { transform: translateY(0);    box-shadow: 0 2px 10px rgba(67,97,238,.3);  }

/* Version */
.lp-version {
    text-align: center;
    margin-top: 28px;
    font-size: 11.5px;
    color: #c5cdd8;
}
.lp-version strong { color: #9aa5b8; font-weight: 600; }

/* Captcha */
.lp-captcha { margin-bottom: 20px; }

/* ---- Responsive ---- */
@media (max-width: 768px) {
    .lp-hero  { display: none; }
    .lp-panel { width: 100%; box-shadow: none; }
}
@media (max-width: 480px) {
    .lp-panel   { padding: 36px 24px; }
    .lp-heading { font-size: 22px; }
}
</style>

<div class="lp-wrap">

    <!-- ======= LEFT: Hero Image ======= -->
    <div class="lp-hero">
        <div class="lp-hero-bg"></div>
    </div>

    <!-- ======= RIGHT: Login Form ======= -->
    <div class="lp-panel">
        <div class="lp-form-inner">

            <!-- School branding -->
            <div class="lp-brand">
                <div class="lp-brand-logo">
                    <?php if(customCompute($siteinfos->photo)): ?>
                    <img src="<?=base_url('uploads/images/'.$siteinfos->photo)?>" alt="Logo">
                    <?php else: ?>
                    <i class="fa fa-graduation-cap"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="lp-brand-name"><?=namesorting($siteinfos->sname, 30)?></p>
                    <p class="lp-brand-tag">School Management System</p>
                </div>
            </div>

            <h2 class="lp-heading">Welcome Back</h2>
            <p class="lp-sub">Sign in to manage your school activities</p>

            <!-- Validation alert -->
            <?php if($form_validation != "No" && customCompute($form_validation)): ?>
            <div class="lp-banner err">
                <i class="fa fa-exclamation-circle"></i>
                <span><?=$form_validation?></span>
                <button class="lp-banner-close" onclick="this.parentNode.style.display='none'">&times;</button>
            </div>
            <?php endif; ?>

            <!-- Success flash -->
            <?php if($this->session->flashdata('reset_success')): ?>
            <div class="lp-banner succ">
                <i class="fa fa-check-circle"></i>
                <span><?=$this->session->flashdata('reset_success')?></span>
            </div>
            <?php endif; ?>

            <form method="post" autocomplete="on">

                <!-- Username -->
                <div class="lp-field">
                    <label class="lp-label" for="lp-usr">Username</label>
                    <div class="lp-field-wrap">
                        <input class="lp-input" id="lp-usr" name="username" type="text"
                            placeholder="Enter your username" autofocus
                            value="<?=set_value('username')?>">
                        <i class="lp-field-icon fa fa-user-o"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="lp-field">
                    <label class="lp-label" for="lp-pwd">Password</label>
                    <div class="lp-field-wrap">
                        <input class="lp-input" id="lp-pwd" name="password"
                            type="password" placeholder="Enter your password">
                        <i class="lp-field-icon lp-pwd-toggle fa fa-eye" id="lp-eye"></i>
                    </div>
                </div>

                <!-- Remember me + Forgot -->
                <div class="lp-extras">
                    <label class="lp-check">
                        <input type="checkbox" name="remember" value="Remember Me">
                        Remember me
                    </label>
                    <a class="lp-forgot" href="<?=base_url('reset/index')?>">Forgot Password?</a>
                </div>

                <!-- reCAPTCHA -->
                <?php if(isset($siteinfos->captcha_status) && $siteinfos->captcha_status == 0): ?>
                <div class="lp-captcha">
                    <?php echo $recaptcha['widget']; echo $recaptcha['script']; ?>
                </div>
                <?php endif; ?>

                <!-- Submit -->
                <input type="submit" class="lp-submit" value="Sign In">

            </form>

            <?php if(config_item('demo')): ?><!-- demo spacer --><?php endif; ?>

            <p class="lp-version">version <strong><?=VERSION?></strong></p>

        </div>
    </div>

</div>

<script>
(function () {
    var eye = document.getElementById('lp-eye');
    var pwd = document.getElementById('lp-pwd');
    if (eye && pwd) {
        eye.addEventListener('click', function () {
            var isHidden = pwd.type === 'password';
            pwd.type = isHidden ? 'text' : 'password';
            eye.classList.toggle('fa-eye',      !isHidden);
            eye.classList.toggle('fa-eye-slash',  isHidden);
        });
    }
}());
</script>
