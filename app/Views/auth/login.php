<?php $pageTitle='Đăng nhập'; require_once __DIR__.'/../layouts/header.php'; ?>
<style>
.login-pg{min-height:calc(100vh - 64px);display:flex;align-items:center;justify-content:center;padding:2rem 1rem;position:relative;overflow:hidden;background:linear-gradient(135deg,#0d1117 0%,#1a1a2e 50%,#16213e 100%)}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes blobA{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(40px,-30px) scale(1.1)}66%{transform:translate(-20px,20px) scale(.95)}}
@keyframes blobB{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(-30px,20px) scale(1.08)}66%{transform:translate(25px,-15px) scale(.92)}}
.lbg-blob{position:absolute;border-radius:50%;pointer-events:none;filter:blur(70px)}
.login-wrap{width:100%;max-width:780px;position:relative;z-index:1;display:flex;align-items:center;gap:2.5rem}
.login-col-left{flex:0 0 auto;display:flex;flex-direction:column;align-items:center;animation:fadeUp .4s both}
.login-col-right{flex:1;min-width:0}
.login-brand{text-align:center;margin-bottom:1.1rem;animation:fadeUp .4s both}
.login-card{background:rgba(255,255,255,.06);backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);border:1px solid rgba(255,255,255,.1);border-radius:22px;padding:1.75rem 2rem;box-shadow:0 30px 60px rgba(0,0,0,.55),inset 0 0 0 1px rgba(255,255,255,.05);animation:fadeUp .5s .15s both}
.l-label{display:block;font-weight:600;font-size:.79rem;color:rgba(255,255,255,.55);margin-bottom:.35rem;letter-spacing:.04em;text-transform:uppercase}
.l-input{width:100%;background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);border-radius:10px;padding:.7rem 1rem;color:#f1f5f9;font-size:.9rem;outline:none;transition:border-color .2s,box-shadow .2s;font-family:inherit;box-sizing:border-box}
.l-input::placeholder{color:rgba(255,255,255,.28)}
.l-input:focus{border-color:rgba(229,62,62,.75);box-shadow:0 0 0 3px rgba(229,62,62,.15)}
.l-btn{width:100%;padding:.7rem;background:linear-gradient(135deg,#e53e3e,#b91c1c);color:#fff;border:none;border-radius:10px;font-size:.95rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:transform .15s,box-shadow .2s;box-shadow:0 4px 18px rgba(229,62,62,.38);font-family:inherit}
.l-btn:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(229,62,62,.55)}
.l-btn:active{transform:translateY(0)}
.l-google{display:flex;align-items:center;justify-content:center;gap:.6rem;width:100%;padding:.65rem;background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.14);border-radius:10px;text-decoration:none;font-size:.88rem;font-weight:600;color:rgba(255,255,255,.85);transition:background .2s,border-color .2s,transform .15s}
.l-google:hover{background:rgba(255,255,255,.13);border-color:rgba(255,255,255,.28);transform:translateY(-1px)}
.l-err{background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.28);border-left:3px solid #ef4444;padding:.65rem .9rem;border-radius:8px;margin-bottom:1rem;font-size:.84rem;color:#fca5a5}
@media(max-width:680px){.login-wrap{flex-direction:column;max-width:420px;gap:0}.login-col-left{margin-bottom:.25rem}.login-card{padding:1.5rem 1.25rem}.login-pg{padding:1rem .75rem}}
/* cat styles */
#catWrap{display:flex;flex-direction:column;align-items:center;margin-bottom:.5rem;user-select:none}
#catSvg{overflow:visible;filter:drop-shadow(0 10px 28px rgba(255,140,60,.4))}
#catPawL,#catPawR{transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
#catEyes,#catEyesClosed{transition:opacity .2s}
@keyframes catBob{0%,100%{transform:translateY(0) rotate(0deg)}40%{transform:translateY(-5px) rotate(-2deg)}60%{transform:translateY(-4px) rotate(2deg)}}
@keyframes catShake{0%,100%{transform:rotate(0)}25%{transform:rotate(-5deg)}75%{transform:rotate(5deg)}}
@keyframes catWag{0%,100%{transform:rotate(0deg)}50%{transform:rotate(18deg)}}
#catSvg.state-watch{animation:catBob 2.2s ease-in-out infinite}
#catSvg.state-cover{animation:catShake 1.6s ease-in-out infinite}
#catTail{transform-box:fill-box;transform-origin:15% 85%;animation:catWag 1.6s ease-in-out infinite}
</style>

<div class="login-pg">
  <!-- Background blobs -->
  <div class="lbg-blob" style="width:550px;height:550px;background:radial-gradient(circle,rgba(229,62,62,.13),transparent 70%);top:-160px;left:-160px;animation:blobA 9s ease-in-out infinite"></div>
  <div class="lbg-blob" style="width:420px;height:420px;background:radial-gradient(circle,rgba(99,102,241,.1),transparent 70%);bottom:-120px;right:-120px;animation:blobB 12s ease-in-out infinite"></div>
  <div class="lbg-blob" style="width:280px;height:280px;background:radial-gradient(circle,rgba(59,130,246,.08),transparent 70%);top:45%;left:65%;animation:blobA 14s ease-in-out infinite reverse"></div>

  <div class="login-wrap">
    <!-- Left column: cat -->
    <div class="login-col-left">
      <div id="catWrap">
      <svg id="catSvg" viewBox="0 0 120 115" width="150" height="150">
        <defs>
          <radialGradient id="gHead" cx="45%" cy="38%" r="55%">
            <stop offset="0%" stop-color="#FFB87A"/>
            <stop offset="100%" stop-color="#FF8C3A"/>
          </radialGradient>
          <radialGradient id="gFace" cx="50%" cy="40%" r="55%">
            <stop offset="0%" stop-color="#FFD9B0"/>
            <stop offset="100%" stop-color="#FFC080"/>
          </radialGradient>
        </defs>
        <g id="catTail">
          <path d="M82 104 Q105 95 110 75 Q114 58 100 56" stroke="#FF8C3A" stroke-width="8" fill="none" stroke-linecap="round"/>
          <path d="M82 104 Q105 95 110 75 Q114 58 100 56" stroke="#FFB87A" stroke-width="3" fill="none" stroke-linecap="round" opacity=".5"/>
          <circle cx="100" cy="56" r="7" fill="#FF9A45"/>
          <circle cx="100" cy="56" r="4" fill="#FFB87A"/>
        </g>
        <ellipse cx="60" cy="103" rx="26" ry="14" fill="#FF9A45"/>
        <ellipse cx="60" cy="103" rx="16" ry="10" fill="#FFD9B0"/>
        <circle cx="60" cy="62" r="42" fill="url(#gHead)"/>
        <polygon points="20,50 22,10 50,38" fill="#FF8C3A"/>
        <polygon points="26,46 28,20 46,38" fill="#FFB0C8"/>
        <polygon points="100,50 98,10 70,38" fill="#FF8C3A"/>
        <polygon points="94,46 92,20 74,38" fill="#FFB0C8"/>
        <ellipse cx="60" cy="68" rx="27" ry="22" fill="url(#gFace)"/>
        <path d="M52 28 Q60 24 68 28" stroke="#E07030" stroke-width="2.2" fill="none" stroke-linecap="round" opacity=".5"/>
        <path d="M50 34 Q60 30 70 34" stroke="#E07030" stroke-width="1.8" fill="none" stroke-linecap="round" opacity=".4"/>
        <g id="catEyes">
          <ellipse cx="42" cy="60" rx="11" ry="11" fill="white"/>
          <circle cx="42" cy="60" r="8" fill="#3DAA77"/>
          <ellipse cx="42" cy="60" rx="3" ry="7.5" fill="#1a1a1a"/>
          <circle cx="39" cy="56" r="2.5" fill="white" opacity=".9"/>
          <circle cx="44" cy="62" r="1.2" fill="white" opacity=".5"/>
          <ellipse cx="78" cy="60" rx="11" ry="11" fill="white"/>
          <circle cx="78" cy="60" r="8" fill="#3DAA77"/>
          <ellipse cx="78" cy="60" rx="3" ry="7.5" fill="#1a1a1a"/>
          <circle cx="75" cy="56" r="2.5" fill="white" opacity=".9"/>
          <circle cx="80" cy="62" r="1.2" fill="white" opacity=".5"/>
        </g>
        <g id="catEyesClosed" style="opacity:0;pointer-events:none">
          <path d="M31 60 Q42 51 53 60" stroke="#3d2015" stroke-width="3" fill="none" stroke-linecap="round"/>
          <path d="M67 60 Q78 51 89 60" stroke="#3d2015" stroke-width="3" fill="none" stroke-linecap="round"/>
          <line x1="35" y1="58" x2="33" y2="53" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="42" y1="55" x2="42" y2="50" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="49" y1="58" x2="51" y2="53" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="71" y1="58" x2="69" y2="53" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="78" y1="55" x2="78" y2="50" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
          <line x1="85" y1="58" x2="87" y2="53" stroke="#3d2015" stroke-width="1.5" stroke-linecap="round"/>
        </g>
        <path d="M60 73 L56.5 77.5 Q60 80 63.5 77.5 Z" fill="#FF8FAD"/>
        <path d="M52 81 Q56 87 60 83 Q64 87 68 81" stroke="#C0654A" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="8"  y1="70" x2="48" y2="74" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <line x1="6"  y1="77" x2="48" y2="77" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <line x1="10" y1="84" x2="48" y2="80" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <line x1="112" y1="70" x2="72" y2="74" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <line x1="114" y1="77" x2="72" y2="77" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <line x1="110" y1="84" x2="72" y2="80" stroke="#c8a090" stroke-width="1.2" stroke-linecap="round" opacity=".7"/>
        <ellipse cx="26" cy="76" rx="10" ry="7" fill="rgba(255,140,140,.22)"/>
        <ellipse cx="94" cy="76" rx="10" ry="7" fill="rgba(255,140,140,.22)"/>
        <g id="catPawL" transform="translate(0,48)">
          <rect x="26" y="28" width="20" height="28" rx="10" fill="#FF9A45"/>
          <ellipse cx="36" cy="28" rx="13" ry="10" fill="#FFB87A"/>
          <ellipse cx="27" cy="20" rx="5" ry="4.5" fill="#FFB87A"/>
          <ellipse cx="36" cy="18" rx="5.5" ry="5"  fill="#FFB87A"/>
          <ellipse cx="45" cy="20" rx="5" ry="4.5" fill="#FFB87A"/>
          <ellipse cx="27" cy="20" rx="3" ry="2.8" fill="#FFB0C8"/>
          <ellipse cx="36" cy="18" rx="3.3" ry="3"  fill="#FFB0C8"/>
          <ellipse cx="45" cy="20" rx="3" ry="2.8" fill="#FFB0C8"/>
          <ellipse cx="36" cy="28" rx="5" ry="4" fill="#FFB0C8"/>
        </g>
        <g id="catPawR" transform="translate(0,48)">
          <rect x="74" y="28" width="20" height="28" rx="10" fill="#FF9A45"/>
          <ellipse cx="84" cy="28" rx="13" ry="10" fill="#FFB87A"/>
          <ellipse cx="75" cy="20" rx="5" ry="4.5" fill="#FFB87A"/>
          <ellipse cx="84" cy="18" rx="5.5" ry="5"  fill="#FFB87A"/>
          <ellipse cx="93" cy="20" rx="5" ry="4.5" fill="#FFB87A"/>
          <ellipse cx="75" cy="20" rx="3" ry="2.8" fill="#FFB0C8"/>
          <ellipse cx="84" cy="18" rx="3.3" ry="3"  fill="#FFB0C8"/>
          <ellipse cx="93" cy="20" rx="3" ry="2.8" fill="#FFB0C8"/>
          <ellipse cx="84" cy="28" rx="5" ry="4" fill="#FFB0C8"/>
        </g>
      </svg>
      <p style="margin-top:.75rem;font-size:.78rem;color:rgba(255,255,255,.3);letter-spacing:.05em;text-align:center">Mình trông coi cho bạn 🐾</p>
    </div>
    </div><!-- /login-col-left -->

    <!-- Right column: brand + card -->
    <div class="login-col-right">
    <div class="login-brand">
      <div style="display:inline-flex;align-items:center;gap:.7rem">
        <div style="width:46px;height:46px;background:linear-gradient(135deg,#e53e3e,#b91c1c);border-radius:11px;display:flex;align-items:center;justify-content:center;font-weight:900;color:#fff;font-size:1.1rem;box-shadow:0 4px 18px rgba(229,62,62,.45)">TH</div>
        <div style="text-align:left">
          <div style="font-weight:800;font-size:.95rem;color:#fff;letter-spacing:.02em">TUẤN HUY COMPUTER</div>
          <div style="font-size:.6rem;color:rgba(229,62,62,.9);letter-spacing:3px;font-weight:700">ĐĂNG NHẬP</div>
        </div>
      </div>
    </div>
    <div class="login-card">
      <h2 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.3rem;text-align:center;letter-spacing:.01em">Chào mừng trở lại!</h2>

      <?php if($error): ?>
      <div class="l-err"><i class="fa-solid fa-triangle-exclamation" style="margin-right:.4rem"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div style="margin-bottom:.9rem">
          <label class="l-label">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars(isset($_POST['email'])?$_POST['email']:'') ?>" required placeholder="your@email.com" class="l-input" autocomplete="off">
        </div>
        <div style="margin-bottom:1.1rem;position:relative">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.35rem">
            <label class="l-label" style="margin-bottom:0">Mật khẩu</label>
            <a href="<?= APP_URL ?>/auth/forgot-password" style="font-size:.74rem;color:rgba(229,62,62,.9);text-decoration:none;font-weight:600;transition:color .15s" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='rgba(229,62,62,.9)'">Quên mật khẩu?</a>
          </div>
          <input type="password" id="pw" name="password" required placeholder="••••••••" class="l-input" style="padding-right:3rem" autocomplete="new-password">
          <button type="button" id="pwToggle" onclick="togglePw()" style="position:absolute;right:.75rem;bottom:.65rem;background:none;border:none;cursor:pointer;color:rgba(255,255,255,.35);transition:color .15s;padding:0;line-height:1" onmouseover="this.style.color='rgba(255,255,255,.8)'" onmouseout="this.style.color='rgba(255,255,255,.35)'"><i class="fa-solid fa-eye" id="pwEyeIco"></i></button>
        </div>
        <button type="submit" class="l-btn">Đăng nhập</button>
      </form>

      <?php if(GOOGLE_CLIENT_ID): ?>
      <div style="display:flex;align-items:center;gap:.6rem;margin:.9rem 0 .75rem">
        <div style="flex:1;height:1px;background:rgba(255,255,255,.1)"></div>
        <span style="font-size:.73rem;color:rgba(255,255,255,.28);white-space:nowrap">hoặc</span>
        <div style="flex:1;height:1px;background:rgba(255,255,255,.1)"></div>
      </div>
      <a href="<?= APP_URL ?>/auth/google-login" class="l-google">
        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.08 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.96 2.31-8.16 2.31-6.26 0-11.57-3.59-13.46-8.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
        Đăng nhập bằng Google
      </a>
      <?php endif; ?>

      <div style="text-align:center;margin-top:.9rem;font-size:.82rem;color:rgba(255,255,255,.35)">
        Chưa có tài khoản? <a href="<?= APP_URL ?>/auth/register" style="color:rgba(229,62,62,.9);font-weight:600;text-decoration:none" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='rgba(229,62,62,.9)'">Đăng ký ngay</a>
      </div>
    </div><!-- /login-card -->
    </div><!-- /login-col-right -->
  </div><!-- /login-wrap -->
</div><!-- /login-pg -->

<script>
(function(){
  var svg   = document.getElementById('catSvg');
  var pawL  = document.getElementById('catPawL');
  var pawR  = document.getElementById('catPawR');
  var eyes  = document.getElementById('catEyes');
  var eyesC = document.getElementById('catEyesClosed');
  var pw    = document.getElementById('pw');
  var shown = false;

  function setPaws(up){
    var y = up ? '0' : '48';
    pawL.setAttribute('transform','translate(0,'+y+')');
    pawR.setAttribute('transform','translate(0,'+y+')');
  }
  function setEyes(open){
    eyes.style.opacity  = open ? '1' : '0';
    eyesC.style.opacity = open ? '0' : '1';
  }
  function setState(s){
    svg.setAttribute('class', 'state-' + s);
    if(s === 'cover'){
      setPaws(true); setEyes(false);
    } else {
      setPaws(false); setEyes(true);
    }
  }

  var email = document.querySelector('input[name="email"]');
  if(email) email.addEventListener('focus', function(){ setState('watch'); });

  pw.addEventListener('focus', function(){ setState(shown ? 'peek' : 'cover'); });
  pw.addEventListener('input', function(){ setState(shown ? 'peek' : 'cover'); });
  pw.addEventListener('blur',  function(){ setTimeout(function(){ setState('watch'); }, 180); });

  window.togglePw = function(){
    shown = !shown;
    pw.type = shown ? 'text' : 'password';
    document.getElementById('pwEyeIco').className = shown ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    setState(shown ? 'peek' : 'cover');
  };

  setState('watch');
})();
</script>
<?php require_once __DIR__.'/../layouts/footer.php'; ?>
