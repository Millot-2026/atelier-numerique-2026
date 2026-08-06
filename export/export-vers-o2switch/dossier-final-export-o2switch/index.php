<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>DEV NOMADE - Dashboard</title>

    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --accent-hover: #0ea5e9;
            --red: #ef4444;
            --orange: #f59e0b;
            --green: #22c55e;
        }

        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px;
        }

        .sticky-header-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #fdfbf7;
            border-bottom: 2px solid #2b2b2b;
            padding: 10px 30px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99999;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sticky-header-bar.visible {
            transform: translateY(0);
        }
        .sticky-header-brand {
            font-family: Georgia, serif;
            font-size: 1.1rem;
            font-weight: 900;
            text-transform: uppercase;
            color: #2b2b2b;
            letter-spacing: -0.5px;
        }
        .sticky-header-hamburger {
            cursor: pointer;
            display: flex;
            align-items: center;
            background: #2b2b2b;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--card-bg);
            padding-bottom: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid transparent;
            position: relative;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); }
        .card-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; word-break: break-all; padding-right: 20px; }

        .btn-delete-export {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffffff;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background-color 0.2s ease;
            z-index: 5;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            padding: 0;
            font-size: 0;
        }
        .btn-delete-export::before {
            content: "×";
            color: #0f172a;
            font-size: 26px;
            font-weight: 900;
            line-height: 1;
            display: block;
        }
        .btn-delete-export:hover {
            background: #ef4444;
            transform: scale(1.1);
        }
        .btn-delete-export:hover::before {
            color: #ffffff;
        }

        .badge {
            display: inline-block; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;
            padding: 4px 10px; border-radius: 99px; margin-bottom: 14px; text-transform: uppercase;
            cursor: pointer; user-select: none; transition: transform 0.1s;
        }
        .badge:active { transform: scale(0.95); }
        .badge-validated   { background: rgba(34,197,94,0.15); color: var(--green); }
        .badge-operational { background: rgba(245,158,11,0.15); color: var(--orange); }
        .badge-progress    { background: rgba(239,68,68,0.15); color: var(--red); }
        .badge-wp          { background: rgba(56,189,248,0.15); color: var(--accent); cursor: default; }

        .card-actions { display: flex; flex-direction: column; gap: 8px; }
        .card-link {
            display: inline-block; text-align: center; background-color: var(--accent); color: var(--bg-color);
            text-decoration: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 0.9rem;
            transition: background-color 0.2s;
        }
        .card-link:hover { background-color: var(--accent-hover); }

        .btn-info {
            background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.3); color: var(--accent);
            text-align: center; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem;
            font-weight: 600; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .btn-info:hover { background-color: var(--accent); color: var(--bg-color); border-color: var(--accent); }

        #modulor-overlay {
            position: fixed; inset: 0; background-color: var(--bg-color); z-index: 10000;
            display: flex; flex-direction: column; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease; overflow-y: auto; padding: 40px; box-sizing: border-box;
        }
        #modulor-overlay.active { opacity: 1; pointer-events: auto; }
        .overlay-container { max-width: 900px; width: 100%; margin: 0 auto; position: relative; display: flex; flex-direction: column; }
        .overlay-close {
            position: fixed; top: 30px; right: 40px; background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-main);
            padding: 10px 20px; border-radius: 8px; font-size: 1rem; font-weight: bold;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: all 0.2s; z-index: 10001;
        }
        .overlay-close:hover { background: var(--accent); color: var(--bg-color); }
        #overlay-title { font-size: 2.2rem; color: var(--accent); margin-top: 0; margin-bottom: 15px; }
        #overlay-text { font-size: 1rem; line-height: 1.6; color: var(--text-muted); margin-bottom: 25px; }
        .overlay-image-wrapper { background: var(--card-bg); border-radius: 12px; padding: 10px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        #overlay-img { width: 100%; height: auto; border-radius: 8px; display: block; }

        .level-block { background: rgba(255, 255, 255, 0.03); border-radius: 8px; padding: 15px; margin-bottom: 15px; border: 1px solid rgba(255, 255, 255, 0.05); }
        .level-title { color: var(--accent); font-size: 1.1rem; margin-top: 0; margin-bottom: 8px; border-bottom: 1px solid rgba(56, 189, 248, 0.2); padding-bottom: 4px; }
        .badge-tech { background: rgba(56, 189, 248, 0.15); color: var(--accent); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 5px; display: inline-block; }

        .separator-v2 { border: none; height: 6px; background-color: var(--accent); margin: 40px 0; opacity: 0.8; border-radius: 3px; }

        details.section-block { margin-bottom: 40px; border-radius: 12px; overflow: hidden; }
        details.section-block > summary {
            list-style: none; cursor: pointer; user-select: none; padding: 14px 20px;
            background-color: var(--card-bg); border: 1px solid rgba(56, 189, 248, 0.15);
            border-radius: 12px; display: flex; align-items: center; gap: 12px;
            transition: background-color 0.2s, border-color 0.2s;
        }
        details.section-block > summary::-webkit-details-marker { display: none; }
        details.section-block > summary::marker { display: none; }
        details.section-block > summary:hover { background-color: #243048; border-color: var(--accent); }
        details.section-block > summary h2 { margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--text-main); flex: 1; }
        details.section-block > summary .summary-chevron { font-size: 0.9rem; color: var(--accent); transition: transform 0.25s ease; }
        details.section-block[open] > summary .summary-chevron { transform: rotate(180deg); }
        .section-body { padding: 24px 4px 8px 4px; }

        .news-sheet {
            background-color: #fdfbf7;
            color: #2b2b2b;
            border: 2px solid #2b2b2b;
            border-radius: 4px;
            padding: 35px;
            font-family: Georgia, "Times New Roman", serif;
            margin-bottom: 40px;
            position: relative;
        }

        .news-header-divider {
            border: none;
            border-top: 3px double #333333 !important;
            margin: 15px 0 0 0;
        }

        .news-header-wrapper {
            position: relative;
        }

        .journal-mega-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #FDFBF7 !important;
            border: 2px solid #2b2b2b;
            border-top: none;
            padding: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            z-index: 999;
            font-family: -apple-system, sans-serif;
            box-sizing: border-box;
        }
        .journal-mega-menu.active {
            display: block;
        }
        
        body.is-scrolled .news-header-wrapper .journal-mega-menu.active {
            position: fixed !important;
            top: 48px !important;
            left: 30px !important;
            right: 30px !important;
            max-width: none !important;
            box-sizing: border-box !important;
        }
        
        .mega-menu-close-btn {
            display: none;
        }

        .mega-menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .mega-menu-col h4 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c0392b;
            border-bottom: 1px solid #2b2b2b;
            padding-bottom: 6px;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .mega-menu-col ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .mega-menu-col li {
            margin-bottom: 8px;
        }
        .mega-menu-col a {
            color: #2b2b2b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: block;
            padding: 6px 0 6px 12px;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .mega-menu-col a:hover {
            color: #ffffff !important;
            background-color: #555555 !important;
            text-decoration: none !important;
            padding-left: 12px !important;
            padding-right: 8px;
            border-radius: 4px;
        }
        .mega-menu-col a:active, .mega-menu-col a.clicked {
            color: #ffffff !important;
            background-color: #000000 !important;
            text-decoration: none !important;
            padding-left: 12px !important;
            padding-right: 8px;
            border-radius: 4px;
        }

        .news-bandeau {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-bottom: 1px solid #333333;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-weight: 700;
            color: #4a4a4a;
        }

        .news-header-grid {
            display: grid;
            grid-template-columns: 180px 1fr 180px;
            align-items: center;
            padding-bottom: 15px;
            text-align: center;
        }
        .news-ear {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            color: #555555;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .news-manchette {
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            color: #2b2b2b;
            margin: 0;
            line-height: 1.1;
            font-family: Georgia, serif;
        }

        .news-tribune {
            border-bottom: 1px solid #333333;
            padding-top: 25px;
            padding-bottom: 20px;
            margin-top: 15px;
            margin-bottom: 25px;
            text-align: justify;
        }
        .news-tribune h3 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #c0392b;
            margin: 0 0 8px 0;
            font-weight: 700;
        }
        .news-tribune p {
            font-size: 1.1rem;
            line-height: 1.5;
            margin: 0;
            font-style: italic;
            color: #333333;
        }

        .news-grid-container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .news-col-12 { grid-column: span 12; }
        .news-col-11 { grid-column: span 11; }
        .news-col-10 { grid-column: span 10; }
        .news-col-9  { grid-column: span 9; }
        .news-col-8  { grid-column: span 8; }
        .news-col-7  { grid-column: span 7; }
        .news-col-6  { grid-column: span 6; }
        .news-col-5  { grid-column: span 5; }
        .news-col-4  { grid-column: span 4; }

        @media (max-width: 1400px) {
            .press-duo-layout {
                display: flex !important;
                flex-direction: column !important;
            }
        }

        @media (max-width: 1024px) {
            .news-col-12, .news-col-11, .news-col-10, .news-col-9, .news-col-8, .news-col-7, .news-col-6, .news-col-5, .news-col-4 { grid-column: span 12; }
        }
        @media (max-width: 768px) {
            .news-col-12, .news-col-11, .news-col-10, .news-col-9, .news-col-8, .news-col-7, .news-col-6, .news-col-5, .news-col-4 { grid-column: span 12; }
            .news-header-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                padding-bottom: 15px;
            }
            .news-header-grid > div:nth-child(1) {
                grid-column: 1 / 2;
                text-align: left !important;
            }
            .news-header-grid > div:nth-child(2) {
                grid-column: 1 / -1;
                grid-row: 1;
                text-align: center !important;
                margin-bottom: 10px;
            }
            .news-header-grid > div:nth-child(3) {
                grid-column: 2 / 3;
                text-align: right !important;
                display: flex;
                flex-direction: column;
                align-items: flex-end !important;
            }
            .news-header-grid > div:nth-child(3) > div:last-child {
                display: flex;
                justify-content: flex-end !important;
                width: 100%;
            }

            .journal-mega-menu.active {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                min-height: 100dvh !important;
                background: #fdfbf7 !important;
                z-index: 99999 !important;
                padding: 20px !important;
                box-sizing: border-box !important;
                overflow-y: auto !important;
                border: none !important;
                box-shadow: none !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
                margin: 0 !important;
            }
            
            .mega-menu-close-btn {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 15px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e2ddd5;
                flex-shrink: 0;
            }
            .mega-menu-close-btn button {
                background: #2b2b2b;
                color: #fff;
                border: none;
                padding: 10px 18px;
                border-radius: 8px;
                font-size: 0.95rem;
                font-weight: bold;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .mega-menu-grid {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                gap: 12px !important;
                transition: all 0.3s ease;
            }
            
            .mega-menu-col {
                background: #fdfbf7;
                border: 1px solid #e2ddd5;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 0 !important;
                transition: order 0.3s ease, flex 0.3s ease;
            }

            .mega-menu-col.is-expanded {
                order: -1;
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
            }

            .mega-menu-col h4 {
                font-size: 1.15rem;
                font-weight: 800;
                margin: 0;
                padding: 15px;
                background: #fff;
                border-bottom: 1px solid #e2ddd5;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                user-select: none;
                flex-shrink: 0;
            }
            .mega-menu-col h4::after {
                content: '▼';
                font-size: 0.75rem;
                color: #c0392b;
                transition: transform 0.25s ease;
            }
            .mega-menu-col.open h4::after {
                transform: rotate(180deg);
            }
            .mega-menu-col ul {
                display: none;
                padding: 0 !important;
                background: #fdfbf7;
                margin: 0 !important;
            }
            .mega-menu-col.open ul {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                justify-content: space-between !important;
            }
            .mega-menu-col.is-expanded.open ul {
                display: flex !important;
                flex-direction: column !important;
                flex: 1 !important;
                justify-content: space-between !important;
            }
            
            .mega-menu-col li {
                margin-bottom: 0 !important;
                border-bottom: 1px dashed rgba(0,0,0,0.08);
                display: flex !important;
                align-items: stretch !important;
                flex: 1 !important;
                width: 100% !important;
            }
            .mega-menu-col li:last-child {
                border-bottom: none;
            }
            
            .mega-menu-col a {
                font-size: 1.1rem;
                font-weight: 700;
                padding: 0 20px;
                width: 100% !important;
                height: 100% !important;
                display: flex !important;
                align-items: center !important;
                text-decoration: none !important;
                color: #2b2b2b;
                background-color: transparent;
                box-sizing: border-box;
                border-radius: 0 !important;
                transition: background-color 0.15s ease, color 0.15s ease;
            }
            
            .mega-menu-col a:hover {
                color: #ffffff !important;
                background-color: #555555 !important;
                text-decoration: none !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                border-radius: 0 !important;
            }
            
            .mega-menu-col a:active, .mega-menu-col a.clicked {
                color: #ffffff !important;
                background-color: #000000 !important;
                text-decoration: none !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                border-radius: 0 !important;
            }
        }

        .news-article {
            background: #fff;
            padding: 22px;
            border: 1px solid #e2ddd5;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
            height: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .news-article h4 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #2b2b2b;
            margin: 0 0 12px 0;
            border-bottom: 2px solid #2b2b2b;
            padding-bottom: 6px;
        }

        .press-figure {
            margin: 0 0 16px 0;
            background: #fdfbf7;
            border: 1px solid #e2ddd5;
            padding: 8px;
            box-sizing: border-box;
        }
        .press-figure img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: 1px solid #dcd7ce;
        }
        
        .press-figure-scrollable img {
            height: 400px !important;
            max-height: 400px !important;
            object-fit: cover !important;
            object-position: top !important;
            overflow-y: auto;
        }

        .press-caption {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            margin-top: 6px;
            font-weight: bold;
            text-align: center;
        }

        .press-duo-layout {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 16px;
            background: #fdfbf7;
            border: 1px solid #e2ddd5;
            padding: 8px;
        }
        .press-duo-layout img {
            width: 100%;
            height: auto;
            max-height: 280px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: 1px solid #dcd7ce;
        }
        .press-duo-text {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #333;
            text-align: justify;
            margin: 0;
        }

        .modulor-carousel-container {
            position: relative;
            margin: 16px 0;
            background: #f8fafc;
            border: none;
            padding: 0;
        }
        .modulor-carousel-track-wrapper {
            overflow: hidden;
            width: 100%;
        }
        .modulor-carousel-track {
            display: flex;
            transition: transform 0.4s ease-in-out;
            will-change: transform;
        }
        .modulor-carousel-slide {
            min-width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .modulor-carousel-slide img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: contain;
            background: #f8fafc;
            display: block;
            border: none;
            box-shadow: none;
        }
        .modulor-carousel-btn {
            position: absolute;
            top: 45%;
            transform: translateY(-50%);
            background: rgba(43, 43, 43, 0.85);
            color: #ffffff !important;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: bold;
            z-index: 10;
            transition: background 0.2s;
            line-height: 1;
        }
        .modulor-carousel-btn:hover {
            background: rgba(43, 43, 43, 1);
        }
        .modulor-carousel-prev { left: 8px; }
        .modulor-carousel-next { right: 8px; }
        .modulor-carousel-dots {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 10px;
        }
        .modulor-carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: background 0.2s;
        }
        .modulor-carousel-dot.active {
            background: #2b2b2b;
        }

        .editor-switch-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
            background: #f1f3f5;
            padding: 8px;
            border-radius: 4px 4px 0 0;
            border: 1px solid #e2ddd5;
            border-bottom: none;
        }
        .editor-switch-btn {
            background: #fff;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 3px;
            color: #333;
            transition: all 0.2s;
            text-align: center;
            line-height: 1.2;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .editor-switch-btn.active, .editor-switch-btn:hover {
            background: #2b2b2b;
            color: #fff;
            border-color: #2b2b2b;
        }
        .editor-switch-hint {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.65rem;
            color: #777;
            background: #f1f3f5;
            padding: 0 8px 6px 8px;
            margin-bottom: 12px;
            border: 1px solid #e2ddd5;
            border-top: none;
            border-radius: 0 0 4px 4px;
            font-style: italic;
        }

        .news-article p.news-pitch::first-letter {
            font-size: 2.8rem;
            float: left;
            line-height: 0.85;
            padding-right: 6px;
            font-weight: bold;
            color: #c0392b;
            font-family: Georgia, serif;
        }
        .news-article p.news-pitch {
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0 0 10px 0;
            text-align: justify;
            color: #333333;
        }

        .news-subhead {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #c0392b;
            margin: 14px 0 4px 0;
            letter-spacing: 0.5px;
        }
        .news-article p {
            font-size: 0.88rem;
            line-height: 1.5;
            margin: 0 0 8px 0;
            text-align: justify;
            color: #4a4a4a;
        }
        .news-article ul { margin: 4px 0 8px 0; padding-left: 1.1em; color: #4a4a4a; }
        .news-article li { font-size: 0.85rem; line-height: 1.4; margin-bottom: 2px; }

        .news-sheet .badge-tech, 
        .news-sheet .press-badge-tech, 
        .news-sheet .badge {
            display: none !important;
        }

        .news-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #333333;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.75rem;
            color: #555555;
            display: flex;
            justify-content: space-between;
            text-transform: uppercase;
            letter-spacing: 1px;
            grid-column: span 12;
        }

        .news-colophon-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #bbbbbb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 0.80rem;
            color: #4a4a4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            grid-column: span 12;
        }

        .news-article-link-container {
            margin-top: 15px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-top: 1px solid #f0ede6;
            padding-top: 8px;
            text-align: right;
        }
        .news-article-link {
            color: #666666;
            text-decoration: none;
            transition: color 0.2s, font-weight 0.2s;
            display: inline-block;
        }
        .news-article-link:hover {
            color: #111111;
            font-weight: bold;
        }

        @media (min-width: 1401px) {
            .press-duo-layout {
                display: grid;
                grid-template-columns: 320px 1fr;
            }
            .desk-col-2 {
                column-count: 2;
                column-gap: 24px;
                text-align: justify;
            }
        }

                /* =========================================================================
            CONTROL PANEL — Volet latéral escamotable (Masqué en mode export)
           ========================================================================= */

        #cp-toggle-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 88888;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #2b2b2b;
            color: #fdfbf7;
            border: 2px solid #555;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.45);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            font-size: 1.2rem;
            line-height: 1;
        }
        #cp-toggle-btn:hover {
            background: #c0392b;
            border-color: #c0392b;
            transform: scale(1.08);
            box-shadow: 0 6px 22px rgba(192,57,43,0.45);
        }
        #cp-toggle-btn.cp-open {
            background: #c0392b;
            border-color: #c0392b;
        }

        #cp-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 88889;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #cp-backdrop.cp-visible {
            opacity: 1;
            pointer-events: auto;
        }

        #control-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 330px;
            max-width: 92vw;
            height: 100vh;
            background: #fdfbf7;
            border-left: 2px solid #2b2b2b;
            z-index: 88890;
            box-shadow: none;
            transform: translateX(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        #control-panel.cp-open {
            transform: translateX(0);
            box-shadow: -6px 0 30px rgba(0,0,0,0.3);
        }

        .cp-header {
            background: #2b2b2b;
            color: #fdfbf7;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            border-bottom: 2px solid #444;
        }
        .cp-header-title {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #fdfbf7;
        }
        .cp-header-subtitle {
            font-size: 0.65rem;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
        }
        .cp-close-btn {
            background: none;
            border: 1px solid #555;
            color: #fdfbf7;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            line-height: 1;
            transition: background 0.2s, border-color 0.2s;
            flex-shrink: 0;
        }
        .cp-close-btn:hover {
            background: #c0392b;
            border-color: #c0392b;
        }

        .cp-actions {
            padding: 12px 18px;
            display: flex;
            gap: 8px;
            border-bottom: 1px solid #e2ddd5;
            flex-shrink: 0;
            background: #f5f0e8;
        }
        .cp-action-btn {
            flex: 1;
            padding: 8px 6px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #2b2b2b;
            background: #fff;
            color: #2b2b2b;
            transition: background 0.2s, color 0.2s;
        }
        .cp-action-btn:hover {
            background: #2b2b2b;
            color: #fff;
        }
        .cp-action-btn.cp-btn-primary {
            background: #2b2b2b;
            color: #fff;
        }
        .cp-action-btn.cp-btn-primary:hover {
            background: #c0392b;
            border-color: #c0392b;
        }
        .cp-action-btn.cp-btn-danger {
            border-color: #c0392b;
            color: #c0392b;
        }
        .cp-action-btn.cp-btn-danger:hover {
            background: #c0392b;
            color: #fff;
        }

        .cp-presets-section {
            padding: 12px 18px;
            background: #efe9df;
            border-bottom: 1px solid #e2ddd5;
            flex-shrink: 0;
        }
        .cp-presets-title {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #c0392b;
            margin-bottom: 8px;
        }
        .cp-presets-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }
        .cp-preset-box {
            background: #fff;
            border: 1px solid #cbd5e1;
            height: 36px;
            font-size: 0.95rem;
            font-weight: 800;
            border-radius: 4px;
            cursor: pointer;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .cp-preset-box.empty {
            color: #94a3b8;
            border-style: dashed;
            background: #f8fafc;
            font-size: 1.2rem;
            font-weight: 900;
        }
        .cp-preset-box.active-preset {
            border: 2px solid #2b2b2b !important;
            box-shadow: 0 0 0 1px #2b2b2b;
        }
        .cp-preset-box:hover {
            background: #2b2b2b;
            color: #fff;
            border-color: #2b2b2b;
            transform: translateY(-2px);
        }
        .cp-presets-hint {
            font-size: 0.65rem;
            color: #777;
            font-style: italic;
            margin-top: 6px;
            text-align: center;
        }

        .cp-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px 18px;
        }
        .cp-body::-webkit-scrollbar {
            width: 5px;
        }
        .cp-body::-webkit-scrollbar-track {
            background: #f5f0e8;
        }
        .cp-body::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 3px;
        }

        .cp-section-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #c0392b;
            border-bottom: 1px solid #e2ddd5;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 14px;
        }
        .cp-section-label:first-of-type {
            margin-top: 0;
        }

        .cp-project-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 0;
            border-bottom: 1px dashed #e2ddd5;
            transition: background 0.15s;
            border-radius: 3px;
            user-select: none;
        }
        .cp-project-row:last-child {
            border-bottom: none;
        }
        .cp-project-row:hover {
            background: #f5f0e8;
            padding-left: 4px;
            padding-right: 4px;
        }

        .cp-toggle {
            position: relative;
            width: 34px;
            height: 18px;
            flex-shrink: 0;
        }
        .cp-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .cp-toggle-slider {
            position: absolute;
            inset: 0;
            background: #cbd5e1;
            border-radius: 20px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .cp-toggle-slider::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            top: 3px;
            left: 3px;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.25);
        }
        .cp-toggle input:checked + .cp-toggle-slider {
            background: #2b2b2b;
        }
        .cp-toggle input:checked + .cp-toggle-slider::before {
            transform: translateX(16px);
        }

        .cp-project-name {
            font-size: 0.78rem;
            font-weight: 600;
            color: #2b2b2b;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cp-project-name.cp-hidden-label {
            color: #aaa;
            text-decoration: line-through;
        }

        .cp-size-select {
            font-size: 0.7rem;
            font-weight: 700;
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #333;
            padding: 2px 4px;
            border-radius: 3px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .cp-move-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex-shrink: 0;
        }
        .cp-move-btn {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #333;
            width: 18px;
            height: 16px;
            font-size: 9px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 2px;
            line-height: 1;
            padding: 0;
        }
        .cp-move-btn:hover {
            background: #2b2b2b;
            color: #fff;
            border-color: #2b2b2b;
        }

        .cp-footer {
            padding: 12px 18px;
            border-top: 1px solid #e2ddd5;
            flex-shrink: 0;
            background: #f5f0e8;
        }
        .cp-apply-btn {
            width: 100%;
            background: #2b2b2b;
            color: #fdfbf7;
            border: 1px solid #2b2b2b;
            padding: 10px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            display: block;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
        }
        .cp-apply-btn:hover {
            background: #c0392b;
            border-color: #c0392b;
            color: #fdfbf7;
            text-decoration: none;
        }
        
        .news-col-hidden {
            display: none !important;
        }
    </style>
</head>
<body>

    <div class="sticky-header-bar" id="sticky-header">
        <div class="sticky-header-brand">
            L'Atelier Numérique
        </div>
        <div class="sticky-header-hamburger" id="sticky-hamburger-btn" title="Menu">
            <svg id="sticky-hamburger-icon-svg" width="20" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <span style="font-family: -apple-system, sans-serif; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-left: 8px; font-weight: bold;">Menu</span>
        </div>
    </div>

    <h1 style="display: none;">🚀 Mes Projets Nomades</h1>

    <div class="news-sheet">
        
        <div class="news-bandeau">
            Chronique Indépendante • Édition Spéciale Nomadisme Numérique • 2026
        </div>

        <div class="news-header-wrapper">
            <div class="news-header-grid" id="original-header-grid">
                <div class="news-ear" style="text-align: left;">
                    <strong>SUPPORT :</strong> Clé USB F:\<br>
                    <strong>SERVEUR :</strong> XAMPP Portable
                </div>
                <div>
                    <h2 class="news-manchette">L'Atelier Numérique</h2>
                    <div style="font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; font-weight: bold; color: #444;">Christophe Millot</div>
                </div>
                <div class="news-ear" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 4px; text-align: right;">
                    <div>
                        <strong>ARCHITECTURES :</strong> Flat-File<br>
                        <strong>STATUT :</strong> Opérationnel
                    </div>
                    <div id="hamburger-menu-btn" style="cursor: pointer; display: flex; align-items: center;" title="Menu">
                        <svg id="hamburger-icon-svg" width="22" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <hr class="news-header-divider">

            <div id="journal-mega-menu" class="journal-mega-menu">
                <div class="mega-menu-close-btn">
                    <button type="button" id="mega-menu-close"><i class="fas fa-times"></i> Fermer</button>
                </div>
                <div class="mega-menu-grid">
                    <div class="mega-menu-col">
                        <h4>Navigation</h4>
                        <ul>
                            <li><a href="page2.php"><i class="fas fa-file-alt"></i> Synthèse (Page 2)</a></li>
                            <li><a href="#top"><i class="fas fa-home"></i> Haut de page</a></li>
                        </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Applications &amp; Modules</h4>
                        <ul>
                                                            <li><a href="#" target="_blank"><i class="fas fa-folder-open"></i> Workstation</a></li>
                                                            <li><a href="/modulor/" target="_blank"><i class="fas fa-folder-open"></i> modulor</a></li>
                                                            <li><a href="/skeletor-v1.0/" target="_blank"><i class="fas fa-folder-open"></i> skeletor-v1.0</a></li>
                                                            <li><a href="/skeletor-v1.0-o2switch/" target="_blank"><i class="fas fa-folder-open"></i> skeletor-v1.0-o2switch</a></li>
                                                            <li><a href="/personator-v1.2/" target="_blank"><i class="fas fa-folder-open"></i> personator-v1.2</a></li>
                                                            <li><a href="/cms-2026-v8-full/" target="_blank"><i class="fas fa-folder-open"></i> cms-2026-v8-full</a></li>
                                                            <li><a href="/texturor/" target="_blank"><i class="fas fa-folder-open"></i> texturor</a></li>
                                                            <li><a href="/user_journey-v1.0/" target="_blank"><i class="fas fa-folder-open"></i> user_journey-v1.0</a></li>
                                                            <li><a href="/wordpress-portable/" target="_blank"><i class="fas fa-folder-open"></i> wordpress-portable</a></li>
                                                            <li><a href="/mon-site/" target="_blank"><i class="fas fa-folder-open"></i> mon-site</a></li>
                                                            <li><a href="/Projet-demo-pour-site-en-ligne/" target="_blank"><i class="fas fa-folder-open"></i> Projet-demo-pour-site-en-ligne</a></li>
                                                    </ul>
                    </div>
                    <div class="mega-menu-col">
                        <h4>Outils &amp; Clé</h4>
                        <ul>
                            <li><a href="#"><i class="fas fa-database"></i> Statuts JSON</a></li>
                            <li><a href="#"><i class="fas fa-info-circle"></i> À propos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="news-tribune">
            <h3>Tribune Libre — L'Affranchissement du Cloud</h3>
            <p>« S'affranchir des infrastructures distantes pour recentrer le développement web sur l'essentiel : la maîtrise absolue du code, de l'octet initial jusqu'au déploiement final, au creux d'un support de poche inaltérable. »</p>
        </div>

        <div class="news-grid-container" id="news-grid-container">
                                            <div class="news-col-12" data-project-name="workstation">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">Workstation</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">SYSTEM</span>
                            </div>
                            
                                                            
                                <p class="news-pitch">Au cœur de l'écosystème portable, <strong>Workstation</strong> s'impose comme le cockpit central et le tableau de bord ultime du développeur nomade. Pensé pour fusionner pilotage du temps, météo en temps réel et utilitaires de code instantanés, il centralise l'ensemble des flux de l'atelier sous une charte graphique sombre et immersive.</p>

                                <div class="news-subhead">Le Cockpit &amp; Contrôles Horaires</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">La partie supérieure réunit les indicateurs vitaux de l'atelier : horloge analogique synchronisée, météo en direct et sélecteur de thèmes dynamiques.</p>
                                
                                <figure class="press-figure">
                                    <img src="images/images-workstation/01-header.png" alt="Header Workstation">
                                    <figcaption class="press-caption">Fig. 1 — En-tête, horloge et widgets de session</figcaption>
                                </figure>

                                <figure class="press-figure">
                                    <img src="images/images-workstation/02-racourcis.png" alt="Raccourcis web">
                                    <figcaption class="press-caption">Fig. 2 — Barre de raccourcis rapides</figcaption>
                                </figure>

                                <div class="news-subhead">Navigation &amp; Utilitaires de Saisie</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">Le lanceur de projets et les générateurs intégrés (tests de polices Google Fonts, convertisseurs PX to REM) offrent une réactivité immédiate sans quitter l'interface.</p>

                                <div class="desk-col-2">
                                    <figure class="press-figure">
                                        <img src="images/images-workstation/03-lanceur.png" alt="Lanceur de projets">
                                        <figcaption class="press-caption">Fig. 3 — Grille du lanceur de projets</figcaption>
                                    </figure>
                                    <figure class="press-figure">
                                        <img src="images/images-workstation/04-google-font-tester.png" alt="Google Font Tester">
                                        <figcaption class="press-caption">Fig. 4 — Module testeur de polices &amp; Lorem</figcaption>
                                    </figure>
                                </div>

                                <div class="news-subhead">Prototypage Live &amp; Playground CSS</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">L'espace d'expérimentation permet de tester du code HTML/CSS à la volée et de manipuler les propriétés graphiques en direct.</p>

                                <figure class="press-figure">
                                    <img src="images/images-workstation/05-codepen.png" alt="Codepen Master">
                                    <figcaption class="press-caption">Fig. 5 — Codepen Master intégré</figcaption>
                                </figure>

                                <figure class="press-figure">
                                    <img src="images/images-workstation/06-css-playground.png" alt="CSS Playground">
                                    <figcaption class="press-caption">Fig. 6 — Playground CSS interactif</figcaption>
                                </figure>

                                <div class="news-subhead">Notes Rapides &amp; Suivi de Projet</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">Le bloc de notes persistantes et le journal de bord structurent la feuille de route et l'évolution de l'application vers un mode natif.</p>

                                <div class="desk-col-2">
                                    <figure class="press-figure">
                                        <img src="images/images-workstation/07-notes-rapides.png" alt="Notes rapides">
                                        <figcaption class="press-caption">Fig. 7 — Bloc notes rapides</figcaption>
                                    </figure>
                                    <figure class="press-figure">
                                        <img src="images/images-workstation/08-journal-de developpement-de-ce-projet.png" alt="Journal de bord">
                                        <figcaption class="press-caption">Fig. 8 — Journal de développement et roadmap</figcaption>
                                    </figure>
                                </div>

                            
                                                            
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/workstation/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-12" data-project-name="modulor">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">modulor</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">CMS</span>
                            </div>
                            
                                                            
                                <p class="news-pitch">Parmi les outils qui composent notre écosystème de développement, certains sont pensés pour produire des livrables finis, tandis que d'autres s'apparentent à de véritables terrains de jeu. <strong>Modulor</strong> appartient incontestablement à cette seconde catégorie.</p>
                                
                                <p>Conçu à l'origine comme une première incursion dans le développement personnel et comme le prétexte à la création d'un site utilitaire sur-mesure, ce module a évolué pour devenir un espace de travail vivant, un laboratoire d'expérimentation visuelle et fonctionnelle taillé pour le web designer et le développeur nomade.</p>

                                <div class="news-subhead">Une architecture piquée d'indépendance</div>
                                <p>D'un point de vue structurel, Modulor ne cherche pas à imposer les contraintes d'un CMS éditorial lourd. L'application repose sur un équilibre technique élégant :</p>
                                <ul>
                                    <li>Un moteur de rendu en amont sous <strong>PHP</strong>, chargé d'hydrater la structure initiale et de gérer l'exportation des travaux.</li>
                                    <li>Une couche de persistance hybride articulée autour d'un fichier central (<code>config.json</code>) pour figer la disposition des blocs sur le serveur local, complétée par un système de secours en <code>localStorage</code>.</li>
                                    <li>Un moteur d'interface en JavaScript pur qui insuffle toute son interactivité à la surface de travail.</li>
                                </ul>
                                <p>Versionné de manière indépendante avec son propre dépôt <code>.git</code>, Modulor vit sa propre vie au cœur de la clé USB, tel un outil en constante mutation.</p>

                                <div class="news-subhead">Galerie &amp; Interfaces Principales</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">Découvrez ci-dessous un aperçu des différents écrans et thèmes de l'application via le carrousel interactif.</p>

                                <div class="modulor-carousel-container" id="modulor-carousel">
                                    <button type="button" class="modulor-carousel-btn modulor-carousel-prev" onclick="moveModulorCarousel(-1)">&#10094;</button>
                                    <button type="button" class="modulor-carousel-btn modulor-carousel-next" onclick="moveModulorCarousel(1)">&#10095;</button>
                                    
                                    <div class="modulor-carousel-track-wrapper">
                                        <div class="modulor-carousel-track" id="modulor-track">
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-01.png" alt="Modulor Accueil 1">
                                            </div>
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-02.png" alt="Modulor Accueil 2">
                                            </div>
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-03.png" alt="Modulor Accueil 3">
                                            </div>
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-04.png" alt="Modulor Accueil 4">
                                            </div>
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-05.png" alt="Modulor Accueil 5">
                                            </div>
                                            <div class="modulor-carousel-slide">
                                                <img src="images/images-modulor/modulor-accueil-06.png" alt="Modulor Accueil 6">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="press-caption" id="modulor-caption" style="margin-top: 8px;">Fig. 1 — Interface Modulor (Vue 1)</div>

                                    <div class="modulor-carousel-dots" id="modulor-dots">
                                        <button type="button" class="modulor-carousel-dot active" onclick="currentModulorSlide(0)"></button>
                                        <button type="button" class="modulor-carousel-dot" onclick="currentModulorSlide(1)"></button>
                                        <button type="button" class="modulor-carousel-dot" onclick="currentModulorSlide(2)"></button>
                                        <button type="button" class="modulor-carousel-dot" onclick="currentModulorSlide(3)"></button>
                                        <button type="button" class="modulor-carousel-dot" onclick="currentModulorSlide(4)"></button>
                                        <button type="button" class="modulor-carousel-dot" onclick="currentModulorSlide(5)"></button>
                                    </div>
                                </div>

                                <div class="news-subhead">Un arsenal d'outils taillé pour le prototypage</div>
                                <p>Ce qui fait la force de Modulor, c'est la concentration d'utilitaires indispensables réunis sur une seule et même interface modulable :</p>
                                <ul>
                                    <li><strong>Le Skin Engine :</strong> véritable caméléon visuel, ce moteur de thèmes permet de basculer instantanément d'une ambiance graphique à une autre (Cyber, Blueprint, V6, Terminal, Neumorph ou Skeletor).</li>
                                    <li><strong>Le Design Lab (CodePen intégré) :</strong> intègre un système d'archives avec animation de retournement (flip), journalisation horodatée et prévisualisation en temps réel via un iframe dynamique.</li>
                                    <li><strong>Les utilitaires d'appoint :</strong> un générateur de faux texte (Lorem Generator) paramétrable à la volée et un explorateur de la base d'icônes FontAwesome chargée localement.</li>
                                    <li><strong>La gestion dynamique des blocs :</strong> composition libre des lignes de travail et organisation du flux d'idées secondé par un bloc-notes persistant.</li>
                                </ul>

                                <div class="news-subhead">L'esprit de l'atelier</div>
                                <p>Modulor incarne parfaitement la philosophie de l'atelier nomade : s'affranchir des architectures complexes pour retrouver le plaisir d'un code maîtrisé, rapide et entièrement sous contrôle local. C'est l'espace où l'on pose les idées, où l'on teste la cohérence d'un composant avant de l'industrialiser.</p>

                            
                                                            
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/modulor/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="skeletor-v1.0">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">skeletor-v1.0</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/capture-skeletor.png" alt="Aperçu Skeletor">
                                    <figcaption class="press-caption">Illustration — Skeletor</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Couteau suisse du développeur nomade, <strong>skeletor-v1.0</strong> transforme la corvée des clics répétés en un jeu d'enfant. Fini les « nouveau dossier », « nouveau fichier index.php » et l'arborescence à recréer à la main : il déploie en un clin d'œil toute la trame de base indispensable pour lancer un nouveau site web, rendant la création de projets à la fois ludique, instantanée et redoutablement efficace.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/skeletor-v1.0/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="skeletor-v1.0-o2switch">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">skeletor-v1.0-o2switch</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/capture-skeletor.png" alt="Aperçu Skeletor">
                                    <figcaption class="press-caption">Illustration — Skeletor</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Version dopée à la production de l'atelier, <strong>skeletor-v1.0-o2switch</strong> reprend la logique ludique et l'enregistrement de trames de son aîné pour l'ériger en rampe de lancement vers le serveur distant. Il évite les manipulations fastidieuses et sécurise d'un bloc le passage de la clé USB nomade à l'hébergeur o2switch, sans friction ni perte de temps.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/skeletor-v1.0-o2switch/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-12" data-project-name="personator-v1.2">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">personator-v1.2</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">CMS</span>
                            </div>
                            
                                                            
                                <p class="news-pitch">Atelier d'incarnation et de génération de profils, <strong>personator-v1.2</strong> donne vie à vos applications en peuplant instantanément vos bases ou vos maquettes avec des données utilisateur sur-mesure, réalistes et percutantes.</p>

                                <div class="news-subhead">L'Art de l'Inincarnation — Structurer le Profil</div>
                                <p>Conçu pour répondre aux exigences des méthodologies UX/UI modernes, Personator structure la création de personas autour de fiches dynamiques multicouches. Chaque profil est rigoureusement documenté pour ancrer les choix de conception dans la réalité des futurs utilisateurs.</p>

                                <div class="news-subhead">Motivation et Frustrations au Cœur de l'Interface</div>
                                <p>L'interface guide l'utilisateur pas à pas à travers les dimensions clés du persona : données démographiques, motivations profondes, freins et habitudes de navigation. Cette cartographie fine permet d'identifier immédiatement les leviers d'engagement et d'anticiper les frictions potentielles sur les maquettes.</p>

                                <div class="news-subhead">Restitution et Export des Fiches Clients</div>
                                <p>Parce qu'un livrable UX doit être aussi élégant à consulter qu'à partager, Personator intègre un double système de sortie : une vue d'impression optimisée pour les formats papier et un archivage rigoureux en JSON, prêt à être réutilisé ou intégré dans les dossiers de conception client.</p>

                            
                                                            
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/personator-v1.2/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="cms-2026-v8-full">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">cms-2026-v8-full</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            
                                <p class="news-pitch">Au cœur de l'atelier nomade se dresse <strong>cms-2026-v8-full</strong>, un système de gestion de contenu sur-mesure et ultra-léger pensé pour s'affranchir des lourdeurs du web traditionnel. Entièrement autonome sur un serveur local XAMPP, il incarne l'excellence du développement Flat-file.</p>

                                <div class="press-duo-layout">
                                    <div>
                                        <img src="images/images-cms/01-desktop-capture-cms-hero.png" alt="Vitrine Hero">
                                        <div class="press-caption">Fig. 1 — En-tête de la vitrine (Hero)</div>
                                    </div>
                                    <div>
                                        <p class="press-duo-text"><strong>La vitrine d'accueil :</strong> Première immersion dans l'écosystème du CMS. Conçu comme une invitation visuelle, ce bandeau d'en-tête (Hero) pose l'ambiance graphique de la plateforme. Pensé pour évoluer, il pourra prochainement s'appuyer sur une bibliothèque d'images pour renouveler l'inspiration à chaque connexion.</p>
                                    </div>
                                </div>

                                <div class="press-duo-layout">
                                    <div>
                                        <img src="images/images-cms/02-desktop-capture-cms-accueil.png" alt="Poste de pilotage">
                                        <div class="press-caption">Fig. 2 — Poste de pilotage (Gestion des Projets)</div>
                                    </div>
                                    <div>
                                        <p class="press-duo-text"><strong>Le poste de pilotage :</strong> Tour de contrôle de l'administrateur unique, cette interface centralise l'accès aux cartes de projets. Chaque espace permet d'initialiser un nouvel article ou de basculer en mode de configuration, garantissant une maîtrise absolue des flux de travail sans aucune dépendance cloud.</p>
                                    </div>
                                </div>

                                <div class="news-subhead">Immersion dans l'Éditeur &amp; Le Responsive</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">La visite se poursuit par la découverte de l'éditeur de pages. Utilisez le sélecteur ci-dessous pour basculer dynamiquement entre l'aperçu Bureau et l'aperçu Mobile de l'interface.</p>
                                
                                <div class="editor-switch-bar">
                                    <button type="button" class="editor-switch-btn active" onclick="switchEditorView('desktop', this)">
                                        <span>Aperçu desktop</span>
                                    </button>
                                    <button type="button" class="editor-switch-btn" onclick="switchEditorView('mobile', this)">
                                        <span>Aperçu mobile</span>
                                    </button>
                                </div>
                                <div class="editor-switch-hint">Cliquez dans l'image pour l'agrandir</div>

                                <figure class="press-figure press-figure-scrollable" id="editor-figure-wrapper">
                                    <img id="editor-demo-img" src="images/images-cms/03-capture-cms-editeur-frame-desktop.png" alt="Éditeur Desktop">
                                    <figcaption class="press-caption" id="editor-demo-caption">Fig. 3 — L'éditeur en mode Bureau (hauteur fixe de 400px avec défilement interne)</figcaption>
                                </figure>

                                <div class="news-subhead">Restitution et Rendu Final</div>
                                <p class="press-duo-text" style="margin-bottom: 10px;">Chaque modification s'exporte et s'affiche instantanément en conditions réelles, garantissant une parfaite lisibilité sur tous les supports d'exploitation.</p>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px;">
                                    <figure class="press-figure" style="margin: 0;">
                                        <img src="images/images-cms/03-desktopcapture cms-site-exemple-page.png" alt="Exemple de page" style="max-height: 250px;">
                                        <figcaption class="press-caption">Fig. 5 — Page exemple générée</figcaption>
                                    </figure>
                                    <figure class="press-figure" style="margin: 0;">
                                        <img src="images/images-cms/05-capture cms-site-mobile.png" alt="Rendu mobile" style="max-height: 250px;">
                                        <figcaption class="press-caption">Fig. 6 — Rendu mobile final</figcaption>
                                    </figure>
                                </div>

                            
                                                            
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/cms-2026-v8-full/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="texturor">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">texturor</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/dashboard/photo-640x480.png" alt="texturor">
                                    <figcaption class="press-caption">Illustration — texturor</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Conçu comme un CodePen <em>home made</em> au cœur de l'atelier, <strong>texturor</strong> est taillé pour prototyper et tester du code en un clin d'œil. Doté d'une capacité redoutable pour enregistrer et organiser tes snippets favoris, il se révèle également parfaitement responsive pour effectuer des tests et des ajustements en ligne directement depuis ton mobile.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/texturor/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="user_journey-v1.0">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">user_journey-v1.0</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/dashboard/photo-640x480.png" alt="user_journey-v1.0">
                                    <figcaption class="press-caption">Illustration — user_journey-v1.0</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Pensé pour façonner et orchestrer les parcours utilisateurs, <strong>user_journey-v1.0</strong> est le seul outil de cette clé entièrement conçu pour l'UX-UI pure. Destiné en priorité absolue aux web designers et aux spécialistes de l'expérience utilisateur, il fournit l'écosystème visuel et fonctionnel idéal pour concevoir, prototyper et évaluer les interfaces avec une finesse absolue.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/user_journey-v1.0/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="wordpress-portable">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">wordpress-portable</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/dashboard/photo-640x480.png" alt="wordpress-portable">
                                    <figcaption class="press-caption">Illustration — wordpress-portable</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Instance WordPress totalement encapsulée et autonome, <strong>wordpress-portable</strong> embarque toute la puissance du CMS le plus populaire du web directement au creux de votre clé USB, sans installation lourde sur la machine hôte.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/wordpress-portable/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="mon-site">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">mon-site</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/dashboard/photo-640x480.png" alt="mon-site">
                                    <figcaption class="press-caption">Illustration — mon-site</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Résultat direct de l'architecture créée avec <strong>Skeletor</strong>, <strong>mon-site</strong> concrétise l'exportation du squelette pour l'afficher et le vérifier directement dans le navigateur en conditions réelles.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/mon-site/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
                                            <div class="news-col-6" data-project-name="Projet-demo-pour-site-en-ligne">
                    <article class="news-article">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 8px;">
                                <h4 style="margin: 0; border: none; padding: 0; font-size: 1.35rem;">Projet-demo-pour-site-en-ligne</h4>
                                <span style="font-family: -apple-system, sans-serif; font-size: 0.65rem; text-transform: uppercase; color: #777; letter-spacing: 0.5px;">UX-UI</span>
                            </div>
                            
                                                            <figure class="press-figure">
                                    <img src="images/dashboard/photo-640x480.png" alt="Projet-demo-pour-site-en-ligne">
                                    <figcaption class="press-caption">Illustration — Projet-demo-pour-site-en-ligne</figcaption>
                                </figure>
                            
                                                            <p class="news-pitch">Description détaillée et présentation complète du projet web : Projet-demo-pour-site-en-ligne.</p>
                            
                                                    </div>
                        <div class="news-article-link-container">
                            <a href="/Projet-demo-pour-site-en-ligne/" class="news-article-link">Voir le projet</a>                        </div>
                    </article>
                </div>
            
            <div class="news-col-12" id="bear-col-block" data-bear-span="12">
                <article class="news-article" style="background: #f8fafc; border: 1px dashed #cbd5e1; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                    <div>
                        <ul style="list-style: none; padding: 0; margin: 0; font-family: -apple-system, sans-serif; font-size: 0.85rem; line-height: 1.8; color: #333;">
                            <li><strong>Rédacteur en chef :</strong> Christophe Millot</li>
                            <li><strong>Assistant :</strong> Gemini</li>
                            <li><strong>Pige :</strong> Antigravity</li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>

        <div class="news-footer">
            <span>&copy; 2026 Christophe Millot • Tous droits réservés</span>
            <span>•</span>
            <span>Mise à jour : 06/08/2026 à 13:22</span>
        </div>

    </div>

        

    

    

    

    
    
    

    

    

    <div id="modulor-overlay">
        <button class="overlay-close" onclick="closeOverlay()"><i class="fas fa-times"></i> Fermer</button>
        <div class="overlay-container">
            <h2 id="overlay-title"></h2>
            <div id="overlay-text"></div>
            <div class="overlay-image-wrapper" id="overlay-img-container">
                <div id="overlay-media-wrapper">
                    <img id="overlay-img" src="" alt="Aperçu de la page d'accueil">
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmExportJournal() {
            if (confirm("Vous êtes sur le point d'exporter le journal vers le dossier Nuxit ? Confirmez-vous ?")) {
                fetch('export/export-vers-o2switch/export-journal.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Exportation réussie ! Le dossier a été généré.");
                            location.reload();
                        } else {
                            alert("Erreur lors de l'exportation.");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Erreur réseau.");
                    });
            }
        }

        const clientPresets = {"preset_1":{"spans":{"workstation":"12","modulor":"12","skeletor-v1.0":"6","skeletor-v1.0-o2switch":"6","personator-v1.2":"12","cms-2026-v8-full":"6","texturor":"6","user_journey-v1.0":"6","wordpress-portable":"6","mon-site":"6","Projet-demo-pour-site-en-ligne":"6"},"order":["workstation","modulor","skeletor-v1.0","skeletor-v1.0-o2switch","personator-v1.2","cms-2026-v8-full","texturor","user_journey-v1.0","wordpress-portable","mon-site","Projet-demo-pour-site-en-ligne"],"name":"Preset 1"},"preset_2":{"spans":{"workstation":"6","modulor":"6","skeletor-v1.0":"6","skeletor-v1.0-o2switch":"6","personator-v1.2":"12","cms-2026-v8-full":"6","texturor":"6","user_journey-v1.0":"6","wordpress-portable":"6","mon-site":"6","Projet-demo-pour-site-en-ligne":"6","export":"6"},"order":["skeletor-v1.0","skeletor-v1.0-o2switch","workstation","modulor","personator-v1.2","cms-2026-v8-full","texturor","user_journey-v1.0","wordpress-portable","mon-site","Projet-demo-pour-site-en-ligne","export"],"name":"Preset 2"},"preset_3":{"spans":{"skeletor-v1.0":"6","modulor":"12","workstation":"12","cms-2026-v8-full":"12","skeletor-v1.0-o2switch":"6","personator-v1.2":"12","texturor":"6","user_journey-v1.0":"6","wordpress-portable":"12","mon-site":"6","Projet-demo-pour-site-en-ligne":"6","export":"6"},"order":["skeletor-v1.0","modulor","workstation","cms-2026-v8-full","skeletor-v1.0-o2switch","personator-v1.2","texturor","user_journey-v1.0","wordpress-portable","mon-site","Projet-demo-pour-site-en-ligne","export"],"name":"Preset 3"},"preset_4":{"spans":{"user_journey-v1.0":"6","personator-v1.2":"12","cms-2026-v8-full":"12","workstation":"12","skeletor-v1.0":"6","skeletor-v1.0-o2switch":"6","modulor":"12","texturor":"6","wordpress-portable":"12","mon-site":"6","Projet-demo-pour-site-en-ligne":"6","export":"6"},"order":["user_journey-v1.0","personator-v1.2","cms-2026-v8-full","workstation","skeletor-v1.0","skeletor-v1.0-o2switch","modulor","texturor","wordpress-portable","mon-site","Projet-demo-pour-site-en-ligne","export"],"name":"Preset 4"},"preset_5":{"spans":{"texturor":"6","skeletor-v1.0":"6","modulor":"12","workstation":"12","cms-2026-v8-full":"12","skeletor-v1.0-o2switch":"6","personator-v1.2":"12","user_journey-v1.0":"6","wordpress-portable":"12","mon-site":"6","Projet-demo-pour-site-en-ligne":"6","export":"6"},"order":["texturor","skeletor-v1.0","modulor","workstation","cms-2026-v8-full","skeletor-v1.0-o2switch","personator-v1.2","user_journey-v1.0","wordpress-portable","mon-site","Projet-demo-pour-site-en-ligne","export"],"name":"Preset 5"}};

        function applyPresetToDOM(presetKey) {
            const preset = clientPresets[presetKey];
            if (!preset) return false;

            const container = document.getElementById('news-grid-container');
            const cpBody = document.getElementById('cp-body');
            const bearBlock = document.getElementById('bear-col-block');

            if (preset.order) {
                const orderArr = Array.isArray(preset.order) ? preset.order : Object.values(preset.order);
                const gridItems = Array.from(container.querySelectorAll('[data-project-name]'));
                const cpRows = cpBody ? Array.from(cpBody.querySelectorAll('.cp-project-row')) : [];
                
                const sortFn = (a, b) => {
                    const nameA = a.getAttribute('data-project-name') || a.getAttribute('data-cp-row');
                    const nameB = b.getAttribute('data-project-name') || b.getAttribute('data-cp-row');
                    const idxA = orderArr.indexOf(nameA);
                    const idxB = orderArr.indexOf(nameB);
                    
                    if (idxA !== -1 && idxB !== -1) return idxA - idxB;
                    if (idxA !== -1) return -1;
                    if (idxB !== -1) return 1;
                    return 0;
                };

                gridItems.sort(sortFn);
                if (cpRows.length > 0) cpRows.sort(sortFn);

                gridItems.forEach(item => {
                    if (bearBlock) {
                        container.insertBefore(item, bearBlock);
                    } else {
                        container.appendChild(item);
                    }
                });

                if (cpBody && cpRows.length > 0) {
                    cpRows.forEach(row => cpBody.appendChild(row));
                }
            }

            if (preset.spans) {
                for (const [pName, spanVal] of Object.entries(preset.spans)) {
                    const sel = cpBody ? cpBody.querySelector('[data-project-size="' + CSS.escape(pName) + '"]') : null;
                    if (sel) {
                        sel.value = spanVal;
                    }
                    document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(pName) + '"]').forEach(col => {
                        col.className = col.className.replace(/news-col-\d+/, '').trim();
                        col.classList.add('news-col-' + spanVal);
                    });
                }
            }

            updateBearSpan();
            return true;
        }

        (function() {
            const activeMenuHref = localStorage.getItem('activeMenuLink');
            if (activeMenuHref) {
                const links = document.querySelectorAll('.mega-menu-col a');
                links.forEach(link => {
                    if (link.getAttribute('href') === activeMenuHref) {
                        link.classList.add('clicked');
                    }
                });
            }

            const serverActivePreset = 1;
            if (serverActivePreset) {
                applyPresetToDOM('preset_' + serverActivePreset);
            }
        })();

        function deleteExport(projectName) {
            if (confirm("⚠️ Voulez-vous vraiment supprimer définitivement le projet \"" + projectName + "\" ainsi que tout son dossier ?")) {
                const formData = new FormData();
                formData.append('action', 'delete_export');
                formData.append('project', projectName);

                fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.getElementById('export-card-' + projectName);
                        if (card) {
                            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                const grid = document.getElementById('exports-grid');
                                if (grid && grid.querySelectorAll('.card').length === 0) {
                                    grid.innerHTML = '<p style="color: var(--text-muted); font-size: 0.9em; padding: 10px;" id="no-exports-msg">Aucun projet généré pour le moment.</p>';
                                }
                            }, 300);
                        }
                    } else {
                        alert("Erreur lors de la suppression du dossier sur le serveur.");
                    }
                })
                .catch(error => {
                    console.error('Erreur réseau :', error);
                    alert("Une erreur réseau est survenue.");
                });
            }
        }

        let modulorSlideIndex = 0;
        function showModulorSlide(index) {
            const track = document.getElementById('modulor-track');
            const dots = document.querySelectorAll('.modulor-carousel-dot');
            const caption = document.getElementById('modulor-caption');
            if (!track) return;
            const slides = track.children;
            if (index >= slides.length) { modulorSlideIndex = 0; }
            else if (index < 0) { modulorSlideIndex = slides.length - 1; }
            else { modulorSlideIndex = index; }
            
            track.style.transform = 'translateX(-' + (modulorSlideIndex * 100) + '%)';
            dots.forEach((dot, idx) => {
                dot.classList.toggle('active', idx === modulorSlideIndex);
            });
            if (caption) {
                caption.innerText = 'Fig. ' + (modulorSlideIndex + 1) + ' — Interface Modulor (Vue ' + (modulorSlideIndex + 1) + ')';
            }
        }
        function moveModulorCarousel(step) {
            showModulorSlide(modulorSlideIndex + step);
        }
        function currentModulorSlide(index) {
            showModulorSlide(index);
        }

        const statusCycle = {
            'validated':   { next: 'operational', label: '&#x1F7E0; Op&eacute;rationnel', class: 'badge badge-operational' },
            'operational': { next: 'progress',    label: '&#x1F534; En cours',        class: 'badge badge-progress' },
            'progress':    { next: 'validated',    label: '&#x1F7E2; Valid&eacute;',    class: 'badge badge-validated' }
        };

        function cycleStatus(badgeEl) {
            const projectName = badgeEl.getAttribute('data-project');
            const currentStatus = badgeEl.getAttribute('data-status');
            
            if (!statusCycle[currentStatus]) return;
            
            const nextData = statusCycle[currentStatus];
            const newStatus = nextData.next;

            badgeEl.setAttribute('data-status', newStatus);
            badgeEl.className = nextData.class;
            badgeEl.innerHTML = nextData.label;

            const formData = new FormData();
            formData.append('project', projectName);
            formData.append('status', newStatus);

            fetch('', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Erreur d’enregistrement côté serveur');
                }
            })
            .catch(error => console.error('Erreur réseau :', error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            const hamburgerBtn = document.getElementById('hamburger-menu-btn');
            const stickyBtn = document.getElementById('sticky-hamburger-btn');
            const megaMenu = document.getElementById('journal-mega-menu');
            const iconSvg = document.getElementById('hamburger-icon-svg');
            const stickyIconSvg = document.getElementById('sticky-hamburger-icon-svg');
            const closeBtn = document.getElementById('mega-menu-close');
            const stickyHeader = document.getElementById('sticky-header');
            const headerWrapper = document.querySelector('.news-header-wrapper');

            if (stickyHeader) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 120) {
                        stickyHeader.classList.add('visible');
                        document.body.classList.add('is-scrolled');
                    } else {
                        stickyHeader.classList.remove('visible');
                        document.body.classList.remove('is-scrolled');
                        if (megaMenu) megaMenu.classList.remove('active');
                        const defaultSvg = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                        if (iconSvg) iconSvg.innerHTML = defaultSvg;
                        if (stickyIconSvg) stickyIconSvg.innerHTML = defaultSvg;
                    }
                });
            }

            function toggleMenu(e) {
                if (e) e.stopPropagation();
                const isOpen = megaMenu.classList.toggle('active');
                
                if (isOpen && window.innerWidth <= 768) {
                    document.body.appendChild(megaMenu);
                } else if (!isOpen && window.innerWidth <= 768 && headerWrapper) {
                    headerWrapper.appendChild(megaMenu);
                }
                
                const svgContent = isOpen 
                    ? '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>'
                    : '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                
                if (iconSvg) iconSvg.innerHTML = svgContent;
                if (stickyIconSvg) stickyIconSvg.innerHTML = svgContent;

                if (!isOpen) {
                    document.querySelectorAll('.mega-menu-col').forEach(c => {
                        c.classList.remove('open', 'is-expanded');
                    });
                }
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleMenu);
            if (stickyBtn) stickyBtn.addEventListener('click', toggleMenu);

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    megaMenu.classList.remove('active');
                    if (window.innerWidth <= 768 && headerWrapper) {
                        headerWrapper.appendChild(megaMenu);
                    }
                    const defaultSvg = '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>';
                    if (iconSvg) iconSvg.innerHTML = defaultSvg;
                    if (stickyIconSvg) stickyIconSvg.innerHTML = defaultSvg;
                    document.querySelectorAll('.mega-menu-col').forEach(c => {
                        c.classList.remove('open', 'is-expanded');
                    });
                });
            }

            const menuLinks = megaMenu ? megaMenu.querySelectorAll('.mega-menu-col a') : [];
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    menuLinks.forEach(l => l.classList.remove('clicked'));
                    this.classList.add('clicked');
                    localStorage.setItem('activeMenuLink', this.getAttribute('href'));
                });
            });

            const megaMenuCols = document.querySelectorAll('.mega-menu-col');
            megaMenuCols.forEach(col => {
                const title = col.querySelector('h4');
                if (title) {
                    title.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const isOpen = col.classList.toggle('open');
                        
                        if (window.innerWidth <= 768) {
                            if (isOpen) {
                                megaMenuCols.forEach(c => {
                                    if (c !== col) {
                                        c.classList.remove('open', 'is-expanded');
                                    }
                                });
                                col.classList.add('is-expanded');
                            } else {
                                col.classList.remove('is-expanded');
                            }
                        }
                    });
                }
            });

            if (megaMenu) {
                megaMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            const cpToggleBtn = document.getElementById('cp-toggle-btn');
            const cpCloseBtn  = document.getElementById('cp-close-btn');
            const cpBackdrop  = document.getElementById('cp-backdrop');
            const cpPanel     = document.getElementById('control-panel');
            const cpSaveBtn   = document.getElementById('cp-save-btn');
            const cpResetBtn  = document.getElementById('cp-reset-btn');
            const cpApplyBtn  = document.getElementById('cp-apply-btn');

            function openCp() {
                cpPanel.classList.add('cp-open');
                cpBackdrop.classList.add('cp-visible');
                cpToggleBtn.classList.add('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeCp() {
                cpPanel.classList.remove('cp-open');
                cpBackdrop.classList.remove('cp-visible');
                cpToggleBtn.classList.remove('cp-open');
                cpToggleBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            if (cpToggleBtn) {
                cpToggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    cpPanel.classList.contains('cp-open') ? closeCp() : openCp();
                });
            }

            if (cpCloseBtn) cpCloseBtn.addEventListener('click', closeCp);
            if (cpBackdrop) cpBackdrop.addEventListener('click', closeCp);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && cpPanel && cpPanel.classList.contains('cp-open')) {
                    closeCp();
                }
            });

            document.querySelectorAll('.cp-preset-box').forEach(presetBtn => {
                presetBtn.onclick = function(e) {
                    e.stopPropagation();

                    const presetNum = parseInt(this.getAttribute('data-preset-num'));
                    const presetKey = 'preset_' + presetNum;
                    const isCtrl = (e.ctrlKey || e.metaKey);
                    const isShift = e.shiftKey;

                    if (isCtrl) {
                        if (clientPresets[presetKey]) {
                            if (confirm("⚠️ Voulez-vous supprimer le Preset " + presetNum + " ?")) {
                                const formData = new FormData();
                                formData.append('action', 'save_config');
                                formData.append('preset_key', presetNum);
                                formData.append('purge', '1');

                                fetch('', { method: 'POST', body: formData })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) window.location.reload();
                                });
                            }
                        }
                        return;
                    }

                    if (!clientPresets[presetKey] || isShift) {
                        const actionMsg = !clientPresets[presetKey] 
                            ? "Le Preset " + presetNum + " est vide. Voulez-vous y enregistrer la disposition actuelle ?"
                            : "Voulez-vous écraser et enregistrer la disposition actuelle dans le Preset " + presetNum + " ?";
                            
                        if (confirm(actionMsg)) {
                            const formData = new FormData();
                            formData.append('action', 'save_config');
                            formData.append('preset_key', presetNum);

                            document.querySelectorAll('.cp-size-select').forEach(sel => {
                                formData.append('spans[' + sel.getAttribute('data-project-size') + ']', sel.value);
                            });

                            const container = document.getElementById('news-grid-container');
                            if (container) {
                                container.querySelectorAll('[data-project-name]').forEach((card, index) => {
                                    formData.append('order[' + index + ']', card.getAttribute('data-project-name'));
                                });
                            }

                            fetch('', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) window.location.reload();
                            });
                        }
                        return;
                    }

                    const formData = new FormData();
                    formData.append('action', 'set_active_preset');
                    formData.append('preset_key', presetNum);

                    fetch('', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                };
            });

            if (saveBtn) {
                saveBtn.onclick = function(e) {
                    e.stopPropagation();
                    const formData = new FormData();
                    formData.append('action', 'save_config');

                    document.querySelectorAll('.cp-size-select').forEach(sel => {
                        formData.append('spans[' + sel.getAttribute('data-project-size') + ']', sel.value);
                    });

                    const container = document.getElementById('news-grid-container');
                    if (container) {
                        container.querySelectorAll('[data-project-name]').forEach((card, index) => {
                            formData.append('order[' + index + ']', card.getAttribute('data-project-name'));
                        });
                    }

                    fetch('', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            saveBtn.innerHTML = '✓ Enregistré !';
                            setTimeout(() => { saveBtn.innerHTML = '✓ Save'; }, 1500);
                        } else {
                            alert('Erreur lors de la sauvegarde.');
                        }
                    })
                    .catch(err => console.error('Erreur réseau :', err));
                };
            }

            if (resetBtn) {
                resetBtn.onclick = function(e) {
                    e.stopPropagation();
                    if (confirm("⚠️ Voulez-vous réinitialiser la mise en page par défaut ?")) {
                        const formData = new FormData();
                        formData.append('action', 'reset_config');
                        fetch('', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) window.location.reload();
                        });
                    }
                };
            }

            if (applyBtn) {
                applyBtn.onclick = function(e) {
                    e.stopPropagation();
                    document.querySelectorAll('.news-grid-container [data-project-name]').forEach(col => {
                        col.className = col.className.replace(/news-col-\d+/, '').trim();
                        col.classList.add('news-col-6');
                    });

                    document.querySelectorAll('.cp-size-select').forEach(sel => {
                        const pName = sel.getAttribute('data-project-size');
                        const spanVal = sel.value;
                        document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(pName) + '"]').forEach(col => {
                            col.className = col.className.replace(/news-col-\d+/, '').trim();
                            col.classList.add('news-col-' + spanVal);
                        });
                    });
                    
                    updateBearSpan();
                };
            }
        });

        function cpToggleProject(projectName, rowEl) {
            const checkbox = rowEl.querySelector('.cp-checkbox');
            if (!checkbox) return;

            const isVisible = checkbox.checked;
            const cols = document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(projectName) + '"]');
            const label = document.getElementById('cp-label-' + projectName);

            cols.forEach(col => {
                if (isVisible) {
                    col.classList.remove('news-col-hidden');
                    col.setAttribute('aria-hidden', 'false');
                } else {
                    col.classList.add('news-col-hidden');
                    col.setAttribute('aria-hidden', 'true');
                }
            });

            if (label) {
                label.classList.toggle('cp-hidden-label', !isVisible);
            }

            updateBearSpan();
        }

        function cpMoveProject(projectName, direction) {
            const container = document.getElementById('news-grid-container');
            const cpBody = document.getElementById('cp-body');
            const bearBlock = document.getElementById('bear-col-block');

            document.querySelectorAll('.news-grid-container [data-project-name="' + CSS.escape(projectName) + '"]').forEach(col => {
                if (direction === -1 && col.previousElementSibling && col.previousElementSibling !== bearBlock) {
                    container.insertBefore(col, col.previousElementSibling);
                } else if (direction === 1 && col.nextElementSibling && col.nextElementSibling !== bearBlock) {
                    container.insertBefore(col.nextElementSibling, col);
                }
            });

            const cpRow = cpBody ? cpBody.querySelector('[data-cp-row="' + CSS.escape(projectName) + '"]') : null;
            if (cpRow) {
                if (direction === -1 && cpRow.previousElementSibling && cpRow.previousElementSibling.classList.contains('cp-project-row')) {
                    cpBody.insertBefore(cpRow, cpRow.previousElementSibling);
                } else if (direction === 1 && cpRow.nextElementSibling && cpRow.nextElementSibling.classList.contains('cp-project-row')) {
                    cpBody.insertBefore(cpRow.nextElementSibling, cpRow);
                }
            }

            updateBearSpan();
        }

        function updateBearSpan() {
            const container = document.getElementById('news-grid-container');
            const bearBlock = document.getElementById('bear-col-block');
            if (!container || !bearBlock) return;

            let currentTotalSpan = 0;
            Array.from(container.children).forEach(child => {
                if (child === bearBlock) return;
                if (!child.classList.contains('news-col-hidden')) {
                    for (let i = 4; i <= 12; i++) {
                        if (child.classList.contains('news-col-' + i)) {
                            currentTotalSpan += i;
                            break;
                        }
                    }
                }
            });

            const remainder = currentTotalSpan % 12;
            let activeSpan = (remainder > 0) ? (12 - remainder) : 6;

            bearBlock.className = bearBlock.className.replace(/news-col-\d+/, '').trim();
            bearBlock.classList.add('news-col-' + activeSpan);
        }
    </script>

</body>
</html>