<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081422">
    <meta name="description" content="Machine Health Operations Portal">
    <link rel="icon" type="image/png" href="{{ asset('dashboard/assets/images/logos/logo.png') }}">
    <title>Machine Health</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #07111e;
            --bg-soft: #0b1929;
            --surface: #102136;
            --surface-2: #12283d;
            --text: #f2f7fd;
            --muted: #9db0c4;
            --soft: #d0deeb;
            --border: rgba(180,207,234,.13);
            --primary: #3797ff;
            --primary-soft: rgba(55,151,255,.12);
            --cyan: #69d2e8;
            --green: #4fd3a1;
            --amber: #ffca76;
            --radius: 20px;
            --shadow: 0 22px 70px rgba(0,0,0,.25);
            --max: 1360px;
        }
        html { scroll-behavior: smooth; scroll-padding-top: 96px; }
        body { margin: 0; min-width: 320px; background: var(--bg); color: var(--text);
            font-family: Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            font-size: 14px; -webkit-font-smoothing: antialiased; }
        body::before { content:""; position: fixed; pointer-events:none; inset:0; z-index:-1;
            background: radial-gradient(circle at 90% 4%, rgba(46,125,215,.12), transparent 28%),
                        radial-gradient(circle at 0% 55%, rgba(45,100,161,.07), transparent 32%); }
        [hidden] { display: none !important; }
        a { color: inherit; text-decoration: none; }
        button, input { font: inherit; }
        button { cursor: pointer; }
        button:disabled { cursor: not-allowed; }
        :focus-visible { outline: 2px solid #8ac7ff; outline-offset: 3px; }
        .container { width: min(calc(100% - 56px),var(--max)); margin-inline:auto; }
        .icon { display:inline-block; width:20px; height:20px; vertical-align:middle; fill:none;
            stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:1.8; flex-shrink:0; }
        .icon.sm { width:16px;height:16px; }.icon.lg{width:26px;height:26px}
        .site-header { position: sticky; top:0; z-index:80; background:rgba(7,17,30,.88);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom:1px solid var(--border); }
        .header-content { min-height:78px; display:flex; align-items:center; justify-content:space-between; gap:22px; }
        .brand { display:flex; align-items:center; gap:12px; min-width:0; }
        .brand-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .brand-name { display:block;font-size:15px;font-weight:850;letter-spacing:.06em; }
        .brand-sub { display:block;margin-top:4px;font-size:10px;letter-spacing:.12em;color:var(--muted);font-weight:700; }
        .desktop-nav { display:flex;align-items:center;gap:31px;font-size:12px;color:#bbcbda;font-weight:700; }
        .desktop-nav a { padding-block:29px;position:relative;transition:color .2s; }
        .desktop-nav a:hover,.desktop-nav a.active{color:#fff}
        .desktop-nav a.active::after { content:"";position:absolute;bottom:-1px;left:0;right:0;height:3px;
            background:var(--primary);border-radius:5px 5px 0 0; }
        .header-actions { display:flex;align-items:center;gap:10px; }
        .icon-btn { display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;
            border:1px solid var(--border);background:rgba(255,255,255,.04);border-radius:12px;color:var(--soft); }
        .icon-btn:hover { background:rgba(255,255,255,.09); }
        .btn { display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:43px;
            padding:10px 17px;border-radius:12px;border:1px solid transparent;font-weight:800;font-size:12px;
            transition:background .2s,border-color .2s,transform .2s; }
        .btn:hover {transform:translateY(-1px)}
        .btn.primary {background:linear-gradient(135deg,#3797ff,#1664ca);color:white;box-shadow:0 10px 22px rgba(29,112,232,.21)}
        .btn.ghost {background:rgba(12,31,50,.47);border-color:rgba(224,240,255,.28);color:white}
        .btn.ghost:hover {background:rgba(255,255,255,.09)}
        .menu-toggle {display:none}
        .mobile-panel{background:#0b1a2c;border-top:1px solid var(--border);padding:8px 28px 16px}
        .mobile-panel a{display:block;padding:13px 8px;color:#d1e1f1;border-bottom:1px solid var(--border);font-weight:650}
        .hero {position:relative; isolation:isolate;min-height:610px;overflow:hidden;border-bottom:1px solid var(--border);
            background:linear-gradient(120deg,#081828 0%,#133451 55%,#0a1b2d 100%)}
        .hero-video,.hero-bg,.hero-mask {position:absolute;inset:0;width:100%;height:100%;pointer-events:none}
        .hero-bg{z-index:-3;background:radial-gradient(circle at 79% 42%,rgba(29,108,170,.45),transparent 38%),
                linear-gradient(145deg,#07192b 10%,#143953 50%,#091626 100%)}
        .hero-video {z-index:-2;object-fit:cover;object-position:center 46%;opacity:.54;filter:saturate(.75) contrast(1.12)}
        .hero-mask {z-index:-1;background:linear-gradient(90deg,rgba(4,13,25,.98) 0%,rgba(6,21,36,.91) 34%,rgba(7,20,34,.65) 67%,rgba(7,19,32,.75)),
            linear-gradient(0deg,#07111e 0%,transparent 45%)}
        .hero-grid {display:grid;grid-template-columns:minmax(0,1.12fr) minmax(340px,.75fr);align-items:center;
            gap:clamp(35px,6vw,110px);min-height:610px;padding-block:70px 80px}
        .eyebrow {display:inline-flex;align-items:center;gap:9px;color:#b3d4f5;font-size:11px;
            font-weight:800;letter-spacing:.20em;text-transform:uppercase}
        .eyebrow::before {content:"";height:2px;width:25px;background:var(--primary);border-radius:99px}
        h1,h2,h3,p{margin-top:0}
        .hero h1 {font-size:clamp(41px,4.65vw,72px);line-height:1.08;letter-spacing:-.052em;
            font-weight:850;max-width:770px;margin:23px 0 21px}
        .hero h1 em {font-style:normal;color:#79baff}
        .hero-description {font-size:15px;color:#c0cfde;line-height:1.85;max-width:570px;margin-bottom:29px}
        .hero-buttons {display:flex;flex-wrap:wrap;gap:12px}
        .hero-tags{display:flex;flex-wrap:wrap;gap:17px;margin-top:36px;font-size:11px;color:#b2c6d9;font-weight:650}
        .hero-tags span{display:flex;align-items:center;gap:7px}.hero-tags .icon{color:#84bcff;width:14px;height:14px}
        .console{background:linear-gradient(155deg,rgba(16,42,67,.93),rgba(9,26,44,.94));border-radius:22px;
            border:1px solid rgba(192,219,245,.2);box-shadow:var(--shadow);backdrop-filter:blur(18px);overflow:hidden}
        .console-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:21px 22px 18px;border-bottom:1px solid var(--border)}
        .console-eyebrow{font-size:10px;color:#a5bdd6;letter-spacing:.16em;font-weight:750;text-transform:uppercase;margin-bottom:6px}
        .console-title{font-size:17px;font-weight:850;margin:0;letter-spacing:-.02em}
        .console-badge{border:1px solid rgba(79,211,161,.23);color:#9ae9ca;background:rgba(79,211,161,.10);
            border-radius:99px;padding:7px 9px;font-size:10px;font-weight:800;white-space:nowrap}
        .console-links{padding:10px}
        .console-item{display:flex;align-items:center;gap:13px;padding:14px 13px;border-radius:12px;
            border:1px solid transparent;transition:background .2s,border-color .2s}
        a.console-item:hover{background:rgba(91,167,248,.08);border-color:rgba(104,170,241,.18)}
        .console-icon{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;
            background:rgba(75,150,240,.13);color:#89c3ff;flex-shrink:0}
        .console-copy{flex:1;min-width:0}
        .console-copy strong{display:block;font-size:12px;font-weight:800;margin-bottom:4px}
        .console-copy small{display:block;font-size:10px;color:#9db5ca;line-height:1.5}
        .console-chevron{color:#80a6cc;display:flex}
        .console-disabled{opacity:.65;cursor:default}
        .console-footer{display:flex;align-items:center;gap:8px;padding:14px 22px;background:rgba(255,255,255,.026);
            border-top:1px solid var(--border);font-size:10px;color:#9cb5ca}
        .console-footer .icon{color:#72dcb1}
        .value-strip{border-bottom:1px solid var(--border);background:#0b1b2d}
        .value-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr))}
        .value-item{display:flex;gap:13px;align-items:center;padding:25px 30px;border-right:1px solid var(--border)}
        .value-item:first-child{padding-left:0}.value-item:last-child{padding-right:0;border-right:0}
        .value-icon{display:grid;place-items:center;width:44px;height:44px;background:rgba(70,155,244,.10);
            border-radius:13px;color:#81bfff;flex:0 0 auto}
        .value-item strong{display:block;font-size:12px;margin-bottom:5px}
        .value-item p{font-size:11px;line-height:1.5;color:#91abc3;margin:0}
        .section{padding:85px 0}
        .section-overline{display:inline-block;color:#80bbff;font-weight:850;font-size:10px;letter-spacing:.2em;text-transform:uppercase;margin-bottom:14px}
        .section-header{display:flex;gap:25px;justify-content:space-between;align-items:end;margin-bottom:28px}
        .section-header h2{font-size:clamp(28px,3vw,42px);line-height:1.18;letter-spacing:-.04em;margin:0 0 11px}
        .section-header p{color:var(--muted);line-height:1.75;max-width:640px;margin:0;font-size:13px}
        .section-count{white-space:nowrap;font-size:11px;color:#92acc4;border:1px solid var(--border);
            border-radius:99px;padding:10px 13px;background:rgba(255,255,255,.025)}
        .tools{display:flex;flex-wrap:wrap;justify-content:space-between;gap:13px;align-items:center;margin-bottom:23px}
        .filters{display:flex;gap:7px;flex-wrap:wrap}
        .filter-chip{border:1px solid var(--border);color:#a9bed2;background:rgba(255,255,255,.026);
            padding:10px 15px;border-radius:99px;font-size:11px;font-weight:800;transition:.18s}
        .filter-chip[aria-pressed="true"]{background:rgba(52,143,238,.16);color:#a8d3ff;border-color:rgba(81,163,250,.45)}
        .filter-chip:hover{border-color:rgba(114,172,226,.55)}
        .search-field{display:flex;align-items:center;gap:10px;min-width:min(100%,275px);
            border-radius:12px;border:1px solid var(--border);background:#0e2033;padding:0 14px;color:#97afc9}
        .search-field:focus-within{border-color:#5299de}
        .search-field input{min-height:43px;min-width:0;width:100%;border:0;background:transparent;color:#fff;outline:none;font-size:12px}
        .search-field input::placeholder{color:#829cb4}
        .kbd{font-size:10px;white-space:nowrap;color:#9aadc0;border:1px solid var(--border);border-radius:5px;padding:2px 5px}
        .module-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
        .module-card{position:relative;display:flex;flex-direction:column;overflow:hidden;min-height:279px;
            border-radius:18px;background:linear-gradient(145deg,#11263c,#0d1f32);border:1px solid var(--border);
            padding:24px;transition:border-color .2s,transform .2s,box-shadow .2s}
        .module-card::before{content:"";position:absolute;right:-84px;top:-92px;width:225px;height:225px;
            border:1px solid rgba(124,179,232,.09);border-radius:50%;box-shadow:0 0 0 32px rgba(100,172,241,.022),0 0 0 64px rgba(100,172,241,.018);pointer-events:none}
        .module-card:hover{transform:translateY(-3px);border-color:rgba(107,179,250,.36);box-shadow:0 22px 44px rgba(0,0,0,.16)}
        .module-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
        .module-icon{width:52px;height:52px;display:grid;place-items:center;background:rgba(61,152,246,.12);
            color:#86c1ff;border:1px solid rgba(82,163,251,.14);border-radius:15px}
        .module-icon.cyan{color:#8de0ed;background:rgba(58,193,216,.09)}
        .module-icon.amber{color:#ffd28c;background:rgba(245,179,76,.09)}
        .module-icon.green{color:#80e6bd;background:rgba(60,205,151,.09)}
        .module-status{font-size:10px;font-weight:750;color:#8db4da;border:1px solid var(--border);
            border-radius:99px;padding:7px 9px;background:rgba(255,255,255,.03)}
        .module-status.available{color:#8de5bb;background:rgba(72,187,135,.08);border-color:rgba(66,190,134,.22)}
        .module-card h3{font-size:17px;letter-spacing:-.025em;margin:25px 0 9px;font-weight:850}
        .module-card p{color:#a3b8cb;font-size:12px;line-height:1.75;margin:0 0 21px;flex:1}
        .module-bottom{display:flex;justify-content:space-between;align-items:center;gap:12px;border-top:1px solid var(--border);padding-top:17px}
        .module-type{color:#839fb6;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.09em}
        .module-cta{display:inline-flex;align-items:center;gap:6px;color:#94c9ff;font-size:11px;font-weight:850}
        .module-cta.off{color:#8196aa;font-weight:700}
        .no-results{padding:30px;border:1px dashed rgba(137,180,222,.27);border-radius:15px;color:var(--muted);text-align:center}
        .work-section{border-top:1px solid var(--border);border-bottom:1px solid var(--border);
            background:radial-gradient(circle at 90% 30%,rgba(34,92,157,.12),transparent 40%),#0a1726}
        .work-grid{display:grid;grid-template-columns:.9fr 1.1fr;gap:55px;align-items:center}
        .work-copy h2{font-size:clamp(29px,3.5vw,43px);letter-spacing:-.045em;line-height:1.15;margin:0 0 15px}
        .work-copy p{font-size:13px;line-height:1.85;color:var(--muted);max-width:470px}
        .work-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:11px}
        .work-step{padding:23px 18px 22px;background:rgba(20,45,70,.75);border:1px solid var(--border);border-radius:16px}
        .step-no{font-size:11px;letter-spacing:.12em;font-weight:900;color:#7cbcff;margin-bottom:28px}
        .work-step .icon{color:#99cfff;width:24px;height:24px;margin-bottom:12px}
        .work-step h3{font-size:13px;margin:0 0 8px}
        .work-step p{margin:0;color:#94abc1;font-size:11px;line-height:1.7}
        .end-banner{position:relative;display:flex;justify-content:space-between;align-items:center;gap:28px;
            padding:36px 42px;border-radius:22px;background:linear-gradient(110deg,#183e66,#11324e 55%,#10283e);
            border:1px solid rgba(134,191,245,.23);overflow:hidden}
        .end-banner::after{content:"";position:absolute;right:3%;top:-120px;width:340px;height:340px;
            border:1px solid rgba(170,219,255,.12);border-radius:50%;pointer-events:none}
        .end-banner h2{font-size:25px;letter-spacing:-.03em;margin:0 0 9px;line-height:1.2}
        .end-banner p{color:#b9d2e6;font-size:12px;line-height:1.7;margin:0}
        .footer{border-top:1px solid var(--border);background:#06101c;padding:30px 0}
        .footer-inner{display:flex;justify-content:space-between;align-items:center;gap:15px;color:#8ca4ba;font-size:11px}
        .footer strong{color:#d8e9f8}
        .video-control{position:absolute;z-index:3;right:20px;bottom:16px;display:inline-flex;align-items:center;gap:7px;
            border:1px solid rgba(211,238,255,.2);border-radius:99px;background:rgba(4,20,36,.66);
            color:#d9eaf8;padding:8px 10px;font-size:10px;font-weight:750;backdrop-filter:blur(10px)}
        .video-control:hover{background:rgba(14,45,72,.9)}
        @media(max-width:1080px){
            .desktop-nav{gap:19px}.hero-grid{gap:28px;grid-template-columns:1fr minmax(300px,.78fr)}
            .hero h1{font-size:clamp(40px,5.1vw,61px)}.work-grid{gap:30px}
        }
        @media(max-width:900px){
            .desktop-nav{display:none}.menu-toggle{display:inline-flex}.header-content{min-height:70px}
            .hero-grid{grid-template-columns:1fr;gap:40px;padding-block:66px 75px}.hero{min-height:0}
            .console{max-width:660px}.module-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
            .work-grid{grid-template-columns:1fr}.work-steps{max-width:100%}
            .value-item{padding:22px 16px}
        }
        @media(max-width:600px){
            .container{width:min(calc(100% - 32px),var(--max))}
            .brand-logo{width:40px;height:40px}.brand-name{font-size:12px}.brand-sub{font-size:9px}
            .header-actions>.btn{display:none}.hero-grid{padding-block:52px 62px}
            .hero h1{font-size:clamp(37px,10.4vw,52px)}.hero-description{font-size:13px;line-height:1.8}
            .hero-buttons .btn{width:100%}.hero-tags{gap:12px;font-size:10px;margin-top:26px}
            .console-head{padding:19px 16px}.console-footer{padding-inline:16px}
            .value-grid{grid-template-columns:1fr}.value-item,.value-item:first-child,.value-item:last-child{
                padding:17px 0;border-right:0;border-bottom:1px solid var(--border)}.value-item:last-child{border-bottom:0}
            .section{padding-block:62px}.section-header{align-items:start;flex-direction:column;gap:15px}
            .section-count{display:none}.tools{align-items:stretch}.search-field{width:100%}
            .module-grid{grid-template-columns:1fr}.module-card{min-height:0;padding:21px}
            .work-steps{grid-template-columns:1fr}.work-step{padding:19px}.step-no{margin-bottom:12px}
            .end-banner{flex-direction:column;align-items:start;padding:29px 23px}.end-banner .btn{width:100%}
            .footer-inner{align-items:flex-start;flex-direction:column}.video-control{right:12px;bottom:11px}
        }
        @media(prefers-reduced-motion:reduce){ html{scroll-behavior:auto}*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important} }
        :root {
            color-scheme: dark;
            --bg: #171c21;
            --bg-soft: #1d242b;
            --surface: #222a32;
            --surface-2: #28323b;
            --text: #edf0f2;
            --muted: #aab3bb;
            --soft: #d4dbe0;
            --border: #39434c;
            --primary: #d99a3d;
            --primary-soft: rgba(217,154,61,.12);
            --cyan: #9ab8c4;
            --green: #7fc49b;
            --amber: #e0ad61;
            --radius: 5px;
            --shadow: none;
            --max: 1280px;
        }
        body { background: var(--bg); font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; }
        body::before { display: none; }
        .container { width: min(calc(100% - 48px),var(--max)); }
        .site-header { background: #202830; border-bottom: 1px solid #3b454e; backdrop-filter: none; }
        .header-content { min-height: 64px; }
        .brand { gap: 10px; }
        .brand-logo { width: 42px; height: 42px; }
        .brand-name { font-size: 12px; font-weight: 700; letter-spacing: .07em; }
        .brand-sub { font-size: 9px; letter-spacing: .04em; font-weight: 500; }
        .desktop-nav { gap: 24px; font-size: 12px; font-weight: 600; }
        .desktop-nav a { padding-block: 23px; }
        .desktop-nav a.active::after { height: 2px; border-radius: 0; }
        .icon-btn, .btn, .filter-chip, .search-field, .section-count, .module-status { border-radius: 4px; }
        .btn { min-height: 37px; padding: 8px 13px; font-size: 11px; font-weight: 650; }
        .btn.primary { background: #d99a3d; color: #171c21; box-shadow: none; }
        .btn.primary:hover { background: #e8aa50; }
        .btn.ghost { background: #252f38; border-color: #56616b; }
        .hero { min-height: 0; background: #202a33; border-bottom: 1px solid #3b454e; }
        .hero-bg { background: linear-gradient(110deg,#202a33,#26333d); }
        .hero-mask { background: linear-gradient(90deg,rgba(24,31,37,.92),rgba(24,31,37,.55)); }
        .hero-grid { min-height: 0; padding-block: 54px 52px; grid-template-columns: minmax(0,1.1fr) minmax(310px,.8fr); gap: 48px; }
        .eyebrow, .section-overline { color: #d9a454; font-size: 10px; letter-spacing: .12em; }
        .eyebrow::before { background: #d9a454; width: 22px; height: 2px; }
        .hero h1 { margin: 17px 0 15px; font-size: clamp(34px,3.6vw,49px); line-height: 1.12; letter-spacing: -.025em; font-weight: 700; }
        .hero h1 em { color: #e3ad5e; }
        .hero-description { max-width: 550px; color: #c0c8ce; font-size: 13px; line-height: 1.7; margin-bottom: 22px; }
        .hero-tags { margin-top: 24px; gap: 18px; font-size: 10px; color: #b6c0c7; }
        .hero-tags .icon { color: #d9a454; }
        .console { background: #252f38; border: 1px solid #46515b; border-radius: 5px; box-shadow: none; backdrop-filter: none; }
        .console-head { padding: 16px 17px; border-bottom: 1px solid #414c55; }
        .console-eyebrow { font-size: 9px; letter-spacing: .1em; }
        .console-title { font-size: 14px; font-weight: 700; }
        .console-badge { border-radius: 3px; color: #e7b66f; background: #3a3328; border-color: #665137; font-size: 9px; }
        .console-links { padding: 5px 8px; }
        .console-item { padding: 11px 9px; border-radius: 3px; }
        a.console-item:hover { background: #303d47; border-color: #596773; }
        .console-icon { width: 32px; height: 32px; border-radius: 4px; color: #e0b36f; background: #3b352c; }
        .console-copy strong { font-size: 11px; font-weight: 650; }
        .console-copy small { font-size: 10px; color: #b0bbc3; }
        .console-footer { padding: 11px 16px; border-top: 1px solid #414c55; font-size: 9px; }
        .value-strip { background: #1d252c; }
        .value-grid { grid-template-columns: repeat(3,minmax(0,1fr)); }
        .value-item { padding: 17px 20px; gap: 10px; }
        .value-icon { width: 34px; height: 34px; border-radius: 4px; color: #e0b36f; background: #39342c; }
        .value-item strong { font-size: 10px; font-weight: 700; }
        .value-item p { font-size: 10px; color: #aeb8c0; }
        .section { padding: 58px 0; }
        .section-header { margin-bottom: 22px; }
        .section-header h2, .work-copy h2 { font-size: clamp(24px,2.5vw,32px); font-weight: 650; letter-spacing: -.02em; }
        .section-header p { font-size: 12px; color: #aeb8c0; }
        .section-count { padding: 8px 10px; font-size: 10px; background: #202a32; }
        .tools { margin-bottom: 16px; }
        .filter-chip { padding: 8px 12px; font-size: 10px; font-weight: 650; background: #202a32; }
        .filter-chip[aria-pressed="true"] { background: #3b3327; color: #edbd78; border-color: #86683f; }
        .search-field { background: #202a32; }
        .search-field input { min-height: 38px; font-size: 11px; }
        .module-grid { grid-template-columns: repeat(3,minmax(0,1fr)); gap: 10px; }
        .module-card { min-height: 220px; padding: 18px; border-radius: 5px; background: #202a32; border: 1px solid #3b454e; box-shadow: none; }
        .module-card::before { display: none; }
        .module-card:hover { transform: none; border-color: #9b7847; box-shadow: none; background: #242f38; }
        .module-icon { width: 37px; height: 37px; border-radius: 4px; color: #e0b36f; background: #39342c; border: 0; }
        .module-icon.cyan { color: #9ab8c4; background: #2e3b42; }
        .module-icon.amber { color: #e0b36f; background: #39342c; }
        .module-icon.green { color: #8fc7a5; background: #2c3b35; }
        .module-status { padding: 5px 7px; font-size: 9px; background: #2b343c; }
        .module-status.available { color: #8fc7a5; background: #293a32; border-color: #45634f; }
        .module-card h3 { margin: 18px 0 7px; font-size: 14px; font-weight: 650; }
        .module-card p { font-size: 11px; line-height: 1.6; color: #b0bac2; }
        .module-bottom { padding-top: 12px; }
        .module-type, .module-cta { font-size: 9px; }
        .module-cta { color: #e2b36c; }
        .work-section { background: #1d252c; border-color: #39434c; }
        .work-grid { gap: 38px; }
        .work-copy p { font-size: 12px; color: #aeb8c0; }
        .work-steps { gap: 8px; }
        .work-step { padding: 16px 13px; border-radius: 4px; background: #252f38; border: 1px solid #3b454e; }
        .step-no { margin-bottom: 18px; color: #d9a454; font-size: 9px; }
        .work-step .icon { width: 20px; height: 20px; color: #d9a454; }
        .work-step h3 { font-size: 11px; font-weight: 650; }
        .work-step p { font-size: 10px; color: #aeb8c0; }
        .end-banner { padding: 25px 28px; border-radius: 5px; background: #252f38; border: 1px solid #46515b; }
        .end-banner::after { display: none; }
        .end-banner h2 { font-size: 20px; font-weight: 650; }
        .end-banner p { font-size: 11px; color: #b0bac2; }
        .footer { padding: 22px 0; background: #141a1f; }
        .footer-inner { font-size: 10px; }
        .footer strong { color: #d5dce1; font-weight: 650; }
        .mobile-panel { background: #202a32; }
        @media(max-width:900px) {
            .hero-grid { grid-template-columns: 1fr; gap: 28px; }
            .console { max-width: 100%; }
            .module-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        }
        @media(max-width:600px) {
            .container { width: min(calc(100% - 30px),var(--max)); }
            .hero-grid { padding-block: 38px; }
            .hero h1 { font-size: 36px; }
            .value-item,.value-item:first-child,.value-item:last-child { padding: 14px 0; }
            .module-grid { grid-template-columns: 1fr; }
            .section { padding-block: 44px; }
            .work-steps { grid-template-columns: 1fr; }
            .end-banner { padding: 22px 18px; }
        }
    </style>
</head>
<body>
@php
    $mhRouteNames = [
        'realtime' => 'realtime-series.index',
        'periodic' => 'periodic-series.index',
        'fault' => 'caterpillar.fault-code.raw-data',
        'induced' => 'operator-induced.index',
    ];
    $mhLinks = [];
    foreach ($mhRouteNames as $key => $routeName) {
        $mhLinks[$key] = \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : null;
    }
@endphp
<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">
    <symbol id="i-activity" viewBox="0 0 24 24"><path d="M3 12h4l3-8 4 16 3-8h4"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m7 15 4-5 3 3 5-7"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><path d="m12 3 10 18H2L12 3Z"/><path d="M12 9v4m0 4h.01"/></symbol>
    <symbol id="i-map" viewBox="0 0 24 24"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15m6-12v15"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="m12 2 9 4v6c0 6-4 9-9 10-5-1-9-4-9-10V6l9-4Z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-6-6 6 6-6 6"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M5 5 19 19M19 5 5 19"/></symbol>
    <symbol id="i-settings" viewBox="0 0 24 24"><path d="M4 7h16M4 17h16M8 4v6m8 4v6"/></symbol>
    <symbol id="i-wrench" viewBox="0 0 24 24"><path d="M14 7a5 5 0 0 0-7 7l-5 5 3 3 5-5a5 5 0 0 0 7-7l-3 3-3-3 3-3Z"/></symbol>
    <symbol id="i-grid" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m4 12 5 5L20 6"/></symbol>
    <symbol id="i-layer" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Zm-9 10 9 5 9-5M3 18l9 5 9-5"/></symbol>
    <symbol id="i-pause" viewBox="0 0 24 24"><path d="M8 5v14m8-14v14"/></symbol>
    <symbol id="i-play" viewBox="0 0 24 24"><path d="m8 4 12 8-12 8V4Z"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2"/></symbol>
</svg>
<header class="site-header" id="top">
    <div class="container header-content">
        <a class="brand" href="#beranda" aria-label="Machine Health - ke beranda">
            <img
            src="{{ asset('dashboard/assets/images/logos/logo.png') }}"
            alt="Machine Health Logo"
            class="brand-logo"
        ><span><strong class="brand-name">MACHINE HEALTH</strong><small class="brand-sub">PT SIMS JAYA KALTIM</small></span>
        </a>
        <nav class="desktop-nav" aria-label="Navigasi utama">
            <a href="#beranda" class="active">Beranda</a>
            <a href="#modules">Modul</a>
            <a href="#workflow">Alur Kerja</a>
            <a href="#support">Informasi</a>
        </nav>
        <div class="header-actions">
            <button class="icon-btn" type="button" id="headerSearch" aria-label="Cari modul" title="Cari modul (/)" ><svg class="icon"><use href="#i-search"/></svg></button>
            <a class="btn primary" href="{{ route('realtime-series.index') }}">Buka Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>
            <button class="icon-btn menu-toggle" type="button" id="menuToggle" aria-label="Buka menu" aria-controls="mobilePanel" aria-expanded="false"><svg class="icon"><use href="#i-menu"/></svg></button>
        </div>
    </div>
    <nav class="mobile-panel" id="mobilePanel" aria-label="Navigasi mobile" hidden>
        <a href="#beranda">Beranda</a><a href="#modules">Modul</a><a href="#workflow">Alur Kerja</a><a href="#support">Informasi</a>
    </nav>
</header>
<main>
    <section class="hero" id="beranda" aria-labelledby="heroTitle">
        <div class="hero-bg"></div>
        <div class="hero-mask"></div>
        <div class="container hero-grid">
            <div class="hero-text">
                <span class="eyebrow">MACHINE HEALTH · OPERATIONS</span>
                <h1 id="heroTitle">Equipment condition<br><em>monitoring portal</em></h1>
                <p class="hero-description">Portal internal untuk memantau parameter equipment, melihat histori data, dan meninjau fault yang tercatat pada unit.</p>
                <div class="hero-buttons">
                    <a href="#modules" class="btn primary">Eksplorasi Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>
                    @if($mhLinks['realtime'])
                        <a href="{{ $mhLinks['realtime'] }}" class="btn ghost"><svg class="icon sm"><use href="#i-activity"/></svg> Buka Realtime Series</a>
                    @else
                        <a href="#workflow" class="btn ghost"><svg class="icon sm"><use href="#i-grid"/></svg> Pelajari Alur Kerja</a>
                    @endif
                </div>
                <div class="hero-tags">
                    <span><svg class="icon"><use href="#i-shield"/></svg> Operational Data</span>
                    <span><svg class="icon"><use href="#i-activity"/></svg> Equipment Health</span>
                    <span><svg class="icon"><use href="#i-chart"/></svg> Trend & Diagnostics</span>
                </div>
            </div>
            <aside class="console" aria-label="Modul operasional">
                <div class="console-head"><div><div class="console-eyebrow">QUICK ACCESS</div><h2 class="console-title">Modul operasional</h2></div><span class="console-badge">INTERNAL</span></div>
                <div class="console-links">
                    @if($mhLinks['realtime'])<a class="console-item" href="{{ $mhLinks['realtime'] }}">@else<div class="console-item console-disabled">@endif
                        <span class="console-icon"><svg class="icon"><use href="#i-activity"/></svg></span>
                        <span class="console-copy"><strong>Realtime Series</strong><small>Parameter kesehatan alat terbaru</small></span>
                        @if($mhLinks['realtime'])<span class="console-chevron"><svg class="icon sm"><use href="#i-arrow"/></svg></span>@endif
                    @if($mhLinks['realtime'])</a>@else</div>@endif
                    @if($mhLinks['periodic'])<a class="console-item" href="{{ $mhLinks['periodic'] }}">@else<div class="console-item console-disabled">@endif
                        <span class="console-icon"><svg class="icon"><use href="#i-chart"/></svg></span>
                        <span class="console-copy"><strong>Periodic Time Series</strong><small>Perbandingan tren dan histori sensor</small></span>
                        @if($mhLinks['periodic'])<span class="console-chevron"><svg class="icon sm"><use href="#i-arrow"/></svg></span>@endif
                    @if($mhLinks['periodic'])</a>@else</div>@endif
                    @if($mhLinks['fault'])<a class="console-item" href="{{ $mhLinks['fault'] }}">@else<div class="console-item console-disabled">@endif
                        <span class="console-icon"><svg class="icon"><use href="#i-alert"/></svg></span>
                        <span class="console-copy"><strong>Fault Code Analysis</strong><small>Review event, severity, dan unit</small></span>
                        @if($mhLinks['fault'])<span class="console-chevron"><svg class="icon sm"><use href="#i-arrow"/></svg></span>@endif
                    @if($mhLinks['fault'])</a>@else</div>@endif
                    @if($mhLinks['induced'])<a class="console-item" href="{{ $mhLinks['induced'] }}">@else<div class="console-item console-disabled">@endif
                        <span class="console-icon"><svg class="icon"><use href="#i-map"/></svg></span>
                        <span class="console-copy"><strong>Operator Induced Map</strong><small>Distribusi fault dan lokasi kejadian</small></span>
                        @if($mhLinks['induced'])<span class="console-chevron"><svg class="icon sm"><use href="#i-arrow"/></svg></span>@endif
                    @if($mhLinks['induced'])</a>@else</div>@endif
                </div>
                <div class="console-footer"><svg class="icon sm"><use href="#i-shield"/></svg> Tautan aktif mengikuti route yang tersedia di Laravel.</div>
            </aside>
        </div>
    </section>
    <div class="value-strip" aria-label="Cakupan portal">
        <div class="container value-grid">
            <div class="value-item"><span class="value-icon"><svg class="icon lg"><use href="#i-activity"/></svg></span><span><strong>Realtime & Historical</strong><p>Monitor kondisi terkini dan pola perubahan sensor</p></span></div>
            <div class="value-item"><span class="value-icon"><svg class="icon lg"><use href="#i-alert"/></svg></span><span><strong>Diagnostics & Fault Events</strong><p>Telusuri gangguan pada unit Caterpillar dan Komatsu</p></span></div>
            <div class="value-item"><span class="value-icon"><svg class="icon lg"><use href="#i-map"/></svg></span><span><strong>Geospatial Analysis</strong><p>Visualisasi kejadian berdasarkan koordinat dan area</p></span></div>
        </div>
    </div>
    <section class="section" id="modules" aria-labelledby="modulesTitle">
        <div class="container">
            <div class="section-header">
                <div><span class="section-overline">MODULE DIRECTORY</span><h2 id="modulesTitle">Daftar modul</h2>
                    <p>Pilih modul yang ingin dibuka. Status menunjukkan apakah halaman sudah tersedia.</p></div>
                <span class="section-count" id="resultCount" aria-live="polite">6 modul tersedia di katalog</span>
            </div>
            <div class="tools">
                <div class="filters" role="group" aria-label="Filter jenis modul">
                    <button class="filter-chip" type="button" data-filter="all" aria-pressed="true">Semua Modul</button>
                    <button class="filter-chip" type="button" data-filter="monitoring" aria-pressed="false">Monitoring</button>
                    <button class="filter-chip" type="button" data-filter="diagnostics" aria-pressed="false">Diagnostics</button>
                    <button class="filter-chip" type="button" data-filter="support" aria-pressed="false">Supporting</button>
                </div>
                <label class="search-field" for="moduleSearch"><svg class="icon sm"><use href="#i-search"/></svg><input id="moduleSearch" type="search" placeholder="Cari modul atau fitur..." autocomplete="off" aria-label="Cari modul"><span class="kbd">/</span></label>
            </div>
            <div class="module-grid" id="moduleGrid">
                <article class="module-card" id="condition" data-category="monitoring" data-search="realtime series monitoring real time machine health condition sensor unit">
                    <div class="module-top"><span class="module-icon"><svg class="icon lg"><use href="#i-activity"/></svg></span><span class="module-status {{ $mhLinks['realtime'] ? 'available' : '' }}">{{ $mhLinks['realtime'] ? 'Terhubung' : 'Belum terhubung' }}</span></div>
                    <h3>Realtime Machine Health</h3><p>Pantau parameter sensor dan kondisi terbaru equipment dalam satu tampilan interaktif.</p>
                    <div class="module-bottom"><span class="module-type">Monitoring</span>
                        @if($mhLinks['realtime'])<a class="module-cta" href="{{ $mhLinks['realtime'] }}">Buka Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>@else<span class="module-cta off">Route belum tersedia</span>@endif</div>
                </article>
                <article class="module-card" id="reports" data-category="monitoring" data-search="periodic time series historical grafik historis tren parameter perbandingan equipment">
                    <div class="module-top"><span class="module-icon cyan"><svg class="icon lg"><use href="#i-chart"/></svg></span><span class="module-status {{ $mhLinks['periodic'] ? 'available' : '' }}">{{ $mhLinks['periodic'] ? 'Terhubung' : 'Belum terhubung' }}</span></div>
                    <h3>Periodic Time Series</h3><p>Analisis grafik historis dan bandingkan parameter sampai empat equipment berdasarkan waktu.</p>
                    <div class="module-bottom"><span class="module-type">Monitoring</span>
                        @if($mhLinks['periodic'])<a class="module-cta" href="{{ $mhLinks['periodic'] }}">Buka Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>@else<span class="module-cta off">Route belum tersedia</span>@endif</div>
                </article>
                <article class="module-card" id="diagnostics" data-category="diagnostics" data-search="caterpillar cat fault code diagnostics severity error code">
                    <div class="module-top"><span class="module-icon amber"><svg class="icon lg"><use href="#i-alert"/></svg></span><span class="module-status {{ $mhLinks['fault'] ? 'available' : '' }}">{{ $mhLinks['fault'] ? 'Terhubung' : 'Belum terhubung' }}</span></div>
                    <h3>Fault Code Analysis</h3><p>Telusuri diagnostic events, severity, timestamp, dan informasi gangguan berdasarkan unit.</p>
                    <div class="module-bottom"><span class="module-type">Diagnostics</span>
                        @if($mhLinks['fault'])<a class="module-cta" href="{{ $mhLinks['fault'] }}">Buka Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>@else<span class="module-cta off">Route belum tersedia</span>@endif</div>
                </article>
                <article class="module-card" data-category="diagnostics" data-search="operator induced distribution map gps geospatial road lokasi cat komatsu fault">
                    <div class="module-top"><span class="module-icon green"><svg class="icon lg"><use href="#i-map"/></svg></span><span class="module-status {{ $mhLinks['induced'] ? 'available' : '' }}">{{ $mhLinks['induced'] ? 'Terhubung' : 'Belum terhubung' }}</span></div>
                    <h3>Operator Induced Distribution</h3><p>Temukan distribusi fault berbasis GPS, klasifikasi, operator, dan equipment pada site map.</p>
                    <div class="module-bottom"><span class="module-type">Diagnostics</span>
                        @if($mhLinks['induced'])<a class="module-cta" href="{{ $mhLinks['induced'] }}">Buka Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a>@else<span class="module-cta off">Route belum tersedia</span>@endif</div>
                </article>
                <article class="module-card" id="predictive" data-category="support" data-search="predictive alerts anomaly threshold critical early warning deteksi dini">
                    <div class="module-top"><span class="module-icon"><svg class="icon lg"><use href="#i-shield"/></svg></span><span class="module-status">Belum terhubung</span></div>
                    <h3>Predictive Alerts</h3><p>Ruang pengembangan untuk analisis pola anomali, parameter threshold, dan peringatan dini.</p>
                    <div class="module-bottom"><span class="module-type">Supporting</span><span class="module-cta off">Route belum dikonfigurasi</span></div>
                </article>
                <article class="module-card" data-category="support" data-search="maintenance planning inspection maintenance schedule rekomendasi tindak lanjut">
                    <div class="module-top"><span class="module-icon cyan"><svg class="icon lg"><use href="#i-wrench"/></svg></span><span class="module-status">Belum terhubung</span></div>
                    <h3>Maintenance Planning</h3><p>Ruang pengembangan untuk prioritas pemeriksaan dan tindak lanjut berdasarkan kondisi alat.</p>
                    <div class="module-bottom"><span class="module-type">Supporting</span><span class="module-cta off">Route belum dikonfigurasi</span></div>
                </article>
            </div>
            <div class="no-results" id="emptyResults" role="status" hidden>Modul tidak ditemukan. Coba kata kunci atau kategori lain.</div>
        </div>
    </section>
    <section class="section work-section" id="workflow" aria-labelledby="workflowTitle">
        <div class="container work-grid">
            <div class="work-copy"><span class="section-overline">HOW IT WORKS</span><h2 id="workflowTitle">Penggunaan sistem</h2>
                <p>Gunakan modul monitoring untuk melihat parameter unit. Buka data historis atau fault code saat diperlukan untuk pemeriksaan lebih lanjut.</p>
            </div>
            <div class="work-steps">
                <div class="work-step"><div class="step-no">01 / MONITOR</div><svg class="icon"><use href="#i-activity"/></svg><h3>Pantau Kondisi</h3><p>Lihat perubahan parameter dan informasi equipment.</p></div>
                <div class="work-step"><div class="step-no">02 / ANALYZE</div><svg class="icon"><use href="#i-chart"/></svg><h3>Analisis Tren</h3><p>Temukan event dan pola yang perlu ditinjau.</p></div>
                <div class="work-step"><div class="step-no">03 / FOLLOW UP</div><svg class="icon"><use href="#i-wrench"/></svg><h3>Tindak Lanjut</h3><p>Gunakan hasil analisis untuk mendukung inspeksi.</p></div>
            </div>
        </div>
    </section>
    <section class="section" aria-label="Ajakan akses modul">
        <div class="container"><div class="end-banner"><div><span class="section-overline">Machine Health Workspace</span><h2>Akses modul Machine Health</h2><p>Pilih modul yang tersedia pada daftar di atas.</p></div>
            <a class="btn primary" href="{{ route('realtime-series.index') }}">Lihat Semua Modul <svg class="icon sm"><use href="#i-arrow"/></svg></a></div></div>
    </section>
</main>
<footer class="footer" id="support">
    <div class="container footer-inner"><div><strong>MACHINE HEALTH <span aria-hidden="true"> - </span> IT SECTION</strong></div><div>Internal Use Only · 2026</div></div>
</footer>
<script>
(function () {
    'use strict';
    const menuButton = document.getElementById('menuToggle');
    const mobilePanel = document.getElementById('mobilePanel');
    const search = document.getElementById('moduleSearch');
    const cards = Array.from(document.querySelectorAll('.module-card'));
    const chips = Array.from(document.querySelectorAll('[data-filter]'));
    const count = document.getElementById('resultCount');
    const empty = document.getElementById('emptyResults');
    let activeFilter = 'all';
    function closeMenu() {
        mobilePanel.hidden = true;
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Buka menu');
    }
    menuButton.addEventListener('click', function () {
        const opening = mobilePanel.hidden;
        mobilePanel.hidden = !opening;
        menuButton.setAttribute('aria-expanded', String(opening));
        menuButton.setAttribute('aria-label', opening ? 'Tutup menu' : 'Buka menu');
    });
    mobilePanel.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', closeMenu); });
    window.addEventListener('resize', function () { if (window.innerWidth > 900) closeMenu(); });
    function applyFilters() {
        const query = search.value.trim().toLocaleLowerCase('id');
        let visible = 0;
        cards.forEach(function (card) {
            const categoryMatch = activeFilter === 'all' || card.dataset.category === activeFilter;
            const searchMatch = (card.dataset.search + ' ' + card.textContent).toLocaleLowerCase('id').includes(query);
            const show = categoryMatch && searchMatch;
            card.hidden = !show;
            if (show) visible += 1;
        });
        count.textContent = visible + ' dari ' + cards.length + ' modul ditampilkan';
        empty.hidden = visible > 0;
    }
    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            activeFilter = chip.dataset.filter;
            chips.forEach(function (item) { item.setAttribute('aria-pressed', String(item === chip)); });
            applyFilters();
        });
    });
    search.addEventListener('input', applyFilters);
    function focusSearch() {
        document.getElementById('modules').scrollIntoView({behavior:'smooth',block:'start'});
        search.focus({preventScroll:true});
    }
    document.getElementById('headerSearch').addEventListener('click', focusSearch);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') { if (document.activeElement === search) search.blur(); closeMenu(); return; }
        const target = event.target;
        const typing = target && (target.matches('input,textarea,select') || target.isContentEditable);
        if (event.key === '/' && !typing && !event.ctrlKey && !event.altKey && !event.metaKey) {
            event.preventDefault(); focusSearch();
        }
    });
    const links = Array.from(document.querySelectorAll('.desktop-nav a'));
    if ('IntersectionObserver' in window) {
        const sections = links.map(function (link) {return document.getElementById(link.getAttribute('href').slice(1));}).filter(Boolean);
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) links.forEach(function (link) {
                    link.classList.toggle('active', link.getAttribute('href') === '#' + entry.target.id);
                });
            });
        }, {rootMargin: '-18% 0px -65% 0px', threshold: 0});
        sections.forEach(function (section) { observer.observe(section); });
    }
    const video = document.getElementById('heroVideo');
    const videoToggle = document.getElementById('videoToggle');
    const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const saveData = navigator.connection && navigator.connection.saveData;
    if (!reduceMotion && !saveData && video && video.dataset.videoSrc) {
        video.src = video.dataset.videoSrc;
        video.muted = true;
        video.addEventListener('canplay', function () {
            video.play().then(function () {videoToggle.hidden = false;}).catch(function () {videoToggle.hidden = true;});
        }, {once:true});
        video.addEventListener('error', function () {videoToggle.hidden = true;});
        videoToggle.addEventListener('click', function () {
            if (video.paused) {video.play().catch(function () {}); videoToggle.innerHTML = '<svg class="icon sm"><use href="#i-pause"/></svg><span>Jeda video</span>';videoToggle.setAttribute('aria-label','Jeda video latar');}
            else {video.pause();videoToggle.innerHTML = '<svg class="icon sm"><use href="#i-play"/></svg><span>Putar video</span>';videoToggle.setAttribute('aria-label','Putar video latar');}
        });
    }
    applyFilters();
})();
</script>
</body>
</html>
