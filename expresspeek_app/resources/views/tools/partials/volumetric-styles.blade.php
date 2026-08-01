<style>
    .vw-page {
        --vw-ink: #eef3ff;
        --vw-muted: #9caed0;
        --vw-violet: #8b6cff;
        --vw-cyan: #39d7ff;
        position: relative;
        isolation: isolate;
        min-height: 760px;
        overflow: hidden;
        border-radius: 28px;
        color: var(--vw-ink);
        background:
            radial-gradient(circle at 12% 18%, rgba(91, 69, 255, .28), transparent 28rem),
            radial-gradient(circle at 88% 74%, rgba(20, 193, 255, .18), transparent 29rem),
            linear-gradient(138deg, #050a1e 0%, #0a1230 48%, #0b173d 100%);
        box-shadow: 0 30px 90px rgba(5, 13, 43, .24);
        font-family: 'Manrope', 'Inter', sans-serif;
    }
    .vw-page::before {
        content: '';
        position: absolute;
        z-index: -2;
        inset: 0;
        opacity: .22;
        background-image:
            linear-gradient(rgba(135, 164, 255, .12) 1px, transparent 1px),
            linear-gradient(90deg, rgba(135, 164, 255, .12) 1px, transparent 1px);
        background-size: 48px 48px;
        -webkit-mask-image: linear-gradient(to bottom, #000, transparent 78%);
        mask-image: linear-gradient(to bottom, #000, transparent 78%);
    }
    .vw-page::after {
        content: '';
        position: absolute;
        z-index: -1;
        width: 480px;
        height: 480px;
        right: -220px;
        top: -220px;
        border: 1px solid rgba(120, 153, 255, .24);
        border-radius: 50%;
        box-shadow: 0 0 0 70px rgba(87, 98, 255, .035), 0 0 0 142px rgba(52, 195, 255, .025);
    }
    .vw-wrap { width: min(1380px, 100%); margin: 0 auto; padding: clamp(34px, 5vw, 76px); }
    .vw-topline { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 44px; }
    .vw-breadcrumb { display: inline-flex; align-items: center; gap: 9px; color: #c0ccec; font-size: 11px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .vw-breadcrumb::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #43e1b5; box-shadow: 0 0 0 6px rgba(67, 225, 181, .12), 0 0 22px rgba(67, 225, 181, .65); }
    .vw-formula-chip { display: flex; align-items: center; gap: 11px; padding: 10px 14px; border: 1px solid rgba(154, 178, 255, .15); border-radius: 999px; color: #aebcdc; background: rgba(255, 255, 255, .055); backdrop-filter: blur(12px); font-size: 11px; }
    .vw-formula-chip strong { color: white; }
    .vw-grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(480px, 1.1fr); gap: clamp(30px, 5vw, 72px); align-items: center; }
    .vw-copy { max-width: 620px; }
    .vw-kicker { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; color: #9db8f7; font-size: 11px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
    .vw-kicker svg { width: 18px; height: 18px; color: #57ddff; }
    .vw-copy h1 { max-width: 590px; margin: 0; color: white; font-size: clamp(42px, 5.1vw, 74px); line-height: .98; letter-spacing: -.065em; font-weight: 800; }
    .vw-copy h1 span { background: linear-gradient(100deg, #a98cff 12%, #6aaeff 55%, #43e2ff); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .vw-copy > p { max-width: 555px; margin: 25px 0 0; color: var(--vw-muted); font-size: 15px; line-height: 1.8; }
    .vw-visual { position: relative; width: min(480px, 92%); margin: 46px auto 0; padding: 22px; border: 1px solid rgba(142, 169, 255, .12); border-radius: 24px; background: linear-gradient(145deg, rgba(255,255,255,.055), rgba(255,255,255,.018)); }
    .vw-visual::after { content: ''; position: absolute; left: 12%; right: 12%; bottom: 11px; height: 18px; border-radius: 50%; background: rgba(44, 128, 255, .36); filter: blur(18px); }
    .vw-box-svg { position: relative; z-index: 1; display: block; width: 100%; height: auto; }
    .vw-svg-edge { fill: rgba(90, 120, 255, .08); stroke: #628cff; stroke-width: 2; }
    .vw-svg-top { fill: rgba(63, 211, 255, .08); stroke: #59d7ff; }
    .vw-svg-arrow { fill: none; stroke: #a894ff; stroke-width: 1.5; stroke-dasharray: 4 5; }
    .vw-svg-label { fill: #dbe4ff; font: 700 12px 'Manrope', sans-serif; letter-spacing: .04em; }
    .vw-card { position: relative; overflow: hidden; padding: clamp(22px, 3vw, 34px); border: 1px solid rgba(167, 188, 255, .18); border-radius: 28px; background: linear-gradient(145deg, rgba(22, 34, 75, .88), rgba(11, 22, 56, .82)); box-shadow: 0 30px 80px rgba(0, 7, 32, .42), inset 0 1px 0 rgba(255,255,255,.08); backdrop-filter: blur(24px); }
    .vw-card::before { content: ''; position: absolute; width: 220px; height: 220px; right: -100px; top: -100px; border-radius: 50%; background: rgba(73, 102, 255, .18); filter: blur(32px); pointer-events: none; }
    .vw-card-head { position: relative; display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; margin-bottom: 26px; }
    .vw-card-title { margin: 0; color: white; font-size: 21px; letter-spacing: -.025em; font-weight: 800; }
    .vw-card-subtitle { margin: 6px 0 0; color: #8fa0c3; font-size: 11px; line-height: 1.6; }
    .vw-live { display: inline-flex; align-items: center; gap: 7px; padding: 7px 10px; border: 1px solid rgba(68, 225, 183, .18); border-radius: 999px; color: #69e9c4; background: rgba(49, 210, 166, .08); font-size: 9px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .vw-live i { width: 6px; height: 6px; border-radius: 50%; background: #49dfb5; box-shadow: 0 0 12px #49dfb5; }
    .vw-dimensions { position: relative; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .vw-field { min-width: 0; }
    .vw-field label { display: flex; align-items: center; justify-content: space-between; gap: 7px; margin-bottom: 8px; color: #bec9e2; font-size: 10px; font-weight: 750; }
    .vw-field label span { color: #7284aa; font-size: 8px; text-transform: uppercase; letter-spacing: .1em; }
    .vw-input-wrap { position: relative; }
    .vw-input { width: 100%; height: 54px; padding: 0 42px 0 15px; border: 1px solid rgba(143, 166, 230, .17); border-radius: 14px; outline: 0; color: white; background: rgba(255,255,255,.055); font-size: 17px; font-weight: 750; transition: border-color .2s, box-shadow .2s, background .2s; }
    .vw-input::placeholder { color: #5f7196; }
    .vw-input:focus { border-color: #7d6cff; background: rgba(255,255,255,.075); box-shadow: 0 0 0 4px rgba(125, 108, 255, .12), 0 0 26px rgba(73, 151, 255, .08); }
    .vw-unit { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); color: #7386af; font-size: 9px; font-weight: 800; pointer-events: none; }
    .vw-secondary-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; padding-top: 18px; border-top: 1px solid rgba(147, 169, 226, .12); }
    .vw-equation { display: flex; align-items: center; justify-content: center; gap: 10px; margin: 21px 0; padding: 13px; border: 1px solid rgba(98, 156, 255, .13); border-radius: 14px; color: #9caed3; background: rgba(42, 78, 145, .12); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11px; text-align: center; }
    .vw-equation strong { color: #dfe7ff; }
    .vw-result { position: relative; overflow: hidden; padding: 22px; border: 1px solid rgba(121, 118, 255, .25); border-radius: 20px; background: linear-gradient(120deg, rgba(95, 67, 224, .28), rgba(29, 116, 190, .18)); }
    .vw-result::after { content: ''; position: absolute; width: 140px; height: 140px; right: -55px; bottom: -70px; border: 1px solid rgba(100, 220, 255, .2); border-radius: 50%; box-shadow: 0 0 0 25px rgba(101, 121, 255, .035); }
    .vw-result-top { position: relative; z-index: 1; display: flex; align-items: flex-end; justify-content: space-between; gap: 18px; }
    .vw-result-label { color: #aab8d7; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .vw-result-number { margin-top: 8px; color: white; font-size: clamp(34px, 4vw, 52px); line-height: 1; letter-spacing: -.05em; font-weight: 800; }
    .vw-result-number small { margin-left: 5px; color: #8fe4ff; font-size: 15px; letter-spacing: 0; }
    .vw-result-meta { color: #aebcdf; font-size: 10px; line-height: 1.6; text-align: right; }
    .vw-result-meta strong { display: block; color: white; font-size: 15px; }
    .vw-actions { display: flex; align-items: center; gap: 10px; margin-top: 18px; }
    .vw-primary, .vw-reset { min-height: 46px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 13px; font-size: 11px; font-weight: 800; transition: .2s ease; }
    .vw-primary { flex: 1; color: white; background: linear-gradient(135deg, #7d5ef5, #5637e5 52%, #267fea); box-shadow: 0 12px 28px rgba(72, 56, 213, .3); }
    .vw-primary:hover { transform: translateY(-2px); filter: brightness(1.08); }
    .vw-primary svg { width: 16px; height: 16px; }
    .vw-reset { width: 92px; border: 1px solid rgba(143, 166, 230, .18); color: #aebbd6; background: rgba(255,255,255,.04); cursor: pointer; }
    .vw-reset:hover { color: white; background: rgba(255,255,255,.08); }
    .vw-trust { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 38px; }
    .vw-trust-item { display: flex; align-items: center; gap: 10px; color: #8fa0c1; font-size: 10px; line-height: 1.45; }
    .vw-trust-icon { width: 32px; height: 32px; flex: 0 0 auto; display: grid; place-items: center; border: 1px solid rgba(126, 153, 227, .14); border-radius: 10px; color: #78dfff; background: rgba(255,255,255,.04); }
    .vw-trust-icon svg { width: 15px; height: 15px; }
    body:not(.role-panel) .vw-page { border-radius: 0; }
    body:not(.role-panel) .vw-wrap { padding-top: 70px; padding-bottom: 80px; }
    @media (max-width: 980px) {
        .vw-page { min-height: 0; }
        .vw-grid { grid-template-columns: 1fr; }
        .vw-copy { max-width: 720px; }
        .vw-copy h1 { max-width: 720px; }
        .vw-visual { display: none; }
        .vw-trust { max-width: 640px; }
    }
    @media (max-width: 640px) {
        .vw-page { border-radius: 20px; }
        .vw-wrap { padding: 26px 16px 30px; }
        body:not(.role-panel) .vw-wrap { padding-top: 42px; padding-bottom: 48px; }
        .vw-topline { align-items: flex-start; flex-direction: column; margin-bottom: 34px; }
        .vw-formula-chip { width: 100%; border-radius: 14px; line-height: 1.45; }
        .vw-copy h1 { font-size: 41px; }
        .vw-copy > p { margin-top: 18px; font-size: 13px; }
        .vw-trust { grid-template-columns: 1fr; margin: 25px 0 8px; }
        .vw-card { padding: 21px 16px; border-radius: 22px; }
        .vw-card-head { margin-bottom: 22px; }
        .vw-live { display: none; }
        .vw-dimensions { gap: 7px; }
        .vw-field label { font-size: 9px; }
        .vw-field label span { display: none; }
        .vw-input { height: 50px; padding-left: 11px; padding-right: 31px; font-size: 15px; }
        .vw-unit { right: 9px; }
        .vw-secondary-fields { grid-template-columns: 1fr; }
        .vw-equation { font-size: 9px; }
        .vw-result-top { align-items: flex-start; flex-direction: column; }
        .vw-result-meta { text-align: left; }
        .vw-actions { align-items: stretch; flex-direction: column; }
        .vw-reset { width: 100%; }
    }
</style>
