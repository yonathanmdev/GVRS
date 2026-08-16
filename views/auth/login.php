<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?= $_ENV['BASE_URL'] ?>/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — GVRS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="public/plugins/fontawesome-free/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #1a1a18;
            --ink-muted: #6b6b63;
            --surface:   #f7f5f0;
            --card:      #ffffff;
            --border:    rgba(26,26,24,0.12);
            --accent:    #2d6a4f;
            --accent-lt: #e8f5ee;
            --accent-dk: #1b4332;
            --danger:    #a32d2d;
            --danger-lt: #fcebeb;
            --radius:    14px;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(26,26,24,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .panel-left {
            position: relative;
            overflow: hidden;
        }

        .panel-left img.banner {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: left center;
            display: block;
        }

        .panel-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                transparent 60%,
                rgba(13, 37, 24, 0.55) 100%
            );
            pointer-events: none;
        }

        .banner-footer {
            position: absolute;
            bottom: 1.5rem;
            left: 1.75rem;
            z-index: 2;
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.04em;
        }

        .panel-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem 5rem;
            background: var(--surface);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .card-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .card-brand img.logo {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            object-fit: contain;
            background: var(--accent-dk);
            padding: 4px;
        }

        .card-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--accent-dk);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: #fff;
            flex-shrink: 0;
        }

        .card-brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 17px;
            color: var(--ink);
            line-height: 1.2;
        }

        .card-brand-sub {
            font-size: 11px;
            color: var(--ink-muted);
            margin-top: 1px;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-eyebrow {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .login-title {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            color: var(--ink);
            line-height: 1.2;
        }

        .login-title span {
            font-style: italic;
            color: var(--ink-muted);
        }

        .amharic-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent-lt);
            color: var(--accent-dk);
            font-size: 13px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(45,106,79,0.15);
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--danger-lt);
            border: 1px solid rgba(163,45,45,0.2);
            border-left: 3px solid var(--danger);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            color: var(--danger);
            margin-bottom: 1.5rem;
            animation: slideIn 0.25s ease;
        }

        .alert i { margin-top: 2px; flex-shrink: 0; }

        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--danger);
            opacity: 0.6;
            padding: 0;
            font-size: 16px;
            line-height: 1;
            flex-shrink: 0;
        }
        .alert-close:hover { opacity: 1; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .field { margin-bottom: 1.25rem; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-muted);
            margin-bottom: 6px;
            letter-spacing: 0.02em;
        }

        .input-wrap { position: relative; }

        .input-wrap .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--ink-muted);
            pointer-events: none;
            transition: color 0.2s;
            z-index: 1;
        }

        .field input {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 40px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--ink);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
        }

        .field input::placeholder { color: rgba(26,26,24,0.3); }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
        }

        .input-wrap:focus-within .field-icon { color: var(--accent); }

        .toggle-pw {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: var(--ink-muted);
            padding: 4px;
            line-height: 1;
            z-index: 1;
        }
        .toggle-pw:hover { color: var(--accent); }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            color: var(--accent);
            text-decoration: none;
            margin-top: -0.75rem;
            margin-bottom: 1.25rem;
            transition: opacity 0.15s;
        }
        .forgot-link:hover { opacity: 0.7; text-decoration: underline; }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            gap: 12px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .remember input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
            flex-shrink: 0;
        }

        .remember span {
            font-size: 13px;
            color: var(--ink-muted);
        }

        .btn-signin {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0 24px;
            height: 48px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.01em;
        }

        .btn-signin:hover  { background: var(--accent-dk); }
        .btn-signin:active { transform: scale(0.97); }
        .btn-signin i { font-size: 13px; transition: transform 0.2s; }
        .btn-signin:hover i { transform: translateX(3px); }

        .login-note {
            margin-top: 2rem;
            font-size: 12px;
            color: var(--ink-muted);
            text-align: center;
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .page { grid-template-columns: 1fr; }
            .panel-left { display: none; }
            .panel-right { padding: 3rem 1.5rem; }
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.1rem;
            background: #eafaf1;
            color: #1e4d38;
            border: 1px solid #b7e4c7;
        }
        .alert-success svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: #2d6a4f;
        }

        /* Language Switcher Login Styling */
        .login-lang-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1.5rem;
        }
        .login-lang-select {
            height: 36px;
            padding: 0 28px 0 12px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: var(--ink);
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%20%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%20%22292.4%22%20height%3D%20%22292.4%22%3E%3Cpath%20fill%3D%20%22%236b6b63%22%20d%3D%20%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.6%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px auto;
        }
        .login-lang-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ── Left panel: banner image ── -->
    <div class="panel-left">
        <img
            class="banner"
            src="public/images/GVRS-login-banner.jpeg"
            alt="GVRS — Government Vehicle Registration made simple"
        >
        <span class="banner-footer">&copy; <?php echo date('Y'); ?> GVRS</span>
    </div>

    <!-- ── Right login panel ── -->
    <div class="panel-right">
        <div class="login-card">

            <!-- Brand row -->
            <div class="card-brand">
                <div class="brand-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div>
                    <!-- Language Switcher Dropdown -->
           <div class="login-lang-wrapper">
    <form method="GET" action="" id="loginLangForm">
        <!-- No hidden action input, and no foreach loop over $_GET -->
        
        <select name="lang" id="loginLangSelect" class="login-lang-select" aria-label="Select Language">
            <option value="am" <?= (($currentLang ?? 'am') === 'am') ? 'selected' : '' ?>>አማርኛ</option>
            <option value="en" <?= (($currentLang ?? 'am') === 'en') ? 'selected' : '' ?>>English</option>
        </select>
    </form>
</div>
                    <div class="card-brand-name"><?= \__('brand_name') ?></div>
                </div>
            </div>

            <div class="login-header">
                <div class="amharic-badge">
                    <i class="fas fa-door-open" style="font-size:12px;"></i>
                   <?= \__('login_page') ?>
                </div>
                <div class="login-title"><?= \__('login_welcome') ?> <span><?= \__('login_back') ?></span></div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert" role="alert" id="error-alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
                <button class="alert-close" onclick="document.getElementById('error-alert').remove()" aria-label="Close">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert-success" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form action="login_process" method="post" novalidate>
                <?= \App\Helpers\Csrf::field(); ?>
                <div class="field">
                    <label for="username"><?= \__('username') ?></label>
                    <div class="input-wrap">
                        <i class="fas fa-user field-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="GVRS000001"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="field">
                    <label for="password"><?= \__('password') ?></label>
                    <div class="input-wrap">
                        <i class="fas fa-lock field-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                            <i class="fas fa-eye" id="togglePwIcon"></i>
                        </button>
                    </div>
                </div>

                <a href="forgot_password" class="forgot-link"><?= \__('forgot_password') ?></a>

                <div class="form-footer">
                    <label class="remember">
                        <input type="checkbox" id="remember" name="remember">
                        <span><?= \__('remember_me') ?></span>
                    </label>

                    <button type="submit" class="btn-signin">
                        <?= \__('sign_in') ?> <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

            </form>

            <p class="login-note">Protected by secure session management. &copy; <?php echo date('Y'); ?> GVRS.</p>

        </div>
    </div>

</div>

<script src="public/plugins/jquery/jquery.min.js"></script>
<script src="public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
    // Show / hide password toggle
    const toggleBtn  = document.getElementById('togglePw');
    const pwInput    = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePwIcon');

    toggleBtn.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type        = isHidden ? 'text' : 'password';
        toggleIcon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        toggleBtn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });

    // Handle form validation intercept
    const form  = document.querySelector('form');
    const usernameInput = document.getElementById('username');

    form.addEventListener('submit', (e) => {
        // Remove any existing error alert box first if they try to click sign-in again
        const existingAlert = document.getElementById('error-alert');
        if (existingAlert) existingAlert.remove();

        // Check if values are completely empty or whitespace
        if (!usernameInput.value.trim() || !pwInput.value.trim()) {
            e.preventDefault(); // Stop form from sending to login_process

            const alertHtml = `
                <div class="alert" role="alert" id="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Please fill in both the username and password fields.</span>
                    <button class="alert-close" onclick="document.getElementById('error-alert').remove()" aria-label="Close">&times;</button>
                </div>
            `;

            form.insertAdjacentHTML('beforebegin', alertHtml);
        }
    });

    // Handle language change dropdown submission
    const langSelect = document.getElementById('loginLangSelect');
    if (langSelect) {
        langSelect.addEventListener('change', () => {
            document.getElementById('loginLangForm').submit();
        });
    }
</script>
</body>
</html>