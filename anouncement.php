<?php
/**
 * Standalone announcement landing page (mobile only).
 * Update $joinLink below with your target URL.
 */
$joinLink = 'https://business.calamueducation.com';
$pageTitle = 'Announcement';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Calamus Education</title>
    <link rel="icon" href="assets/favicon.png" type="image/png">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
        }

        body {
            font-family: Inter, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #005f34;
        }

        .announcement-shell {
            width: 100%;
            margin: 0;
            background: #005f34;
        }

        .announcement-page {
            width: 100%;
        }

        .announcement-hero {
            width: 100%;
            line-height: 0;
        }

        .announcement-hero img {
            display: block;
            width: 100%;
            height: auto;
        }

        .announcement-join-wrap {
            width: 100%;
            padding: 8px 12px;
            padding-bottom: max(12px, env(safe-area-inset-bottom));
            background: #0a3d22;
        }

        .announcement-join {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            min-height: 38px;
            padding: 8px 14px;
            color: #0a3d22;
            background: linear-gradient(180deg, #b9f66d 0%, #7bd43f 100%);
            border: 1.5px solid rgb(255 255 255 / 85%);
            border-radius: 10px;
            box-shadow:
                0 0 0 2px rgb(123 212 63 / 24%),
                0 6px 18px rgb(0 0 0 / 22%);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.2px;
            line-height: 1.2;
            text-transform: uppercase;
            cursor: pointer;
            animation: join-pulse 2s ease-in-out infinite;
            -webkit-tap-highlight-color: transparent;
        }

        .announcement-join:active {
            transform: scale(0.98);
            animation: none;
        }

        .announcement-join-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: rgb(10 61 34 / 12%);
            border-radius: 50%;
            font-size: 10px;
            font-weight: 900;
        }

        @keyframes join-pulse {
            0%,
            100% {
                box-shadow:
                    0 0 0 2px rgb(123 212 63 / 24%),
                    0 6px 18px rgb(0 0 0 / 22%);
            }
            50% {
                box-shadow:
                    0 0 0 4px rgb(123 212 63 / 18%),
                    0 8px 20px rgb(0 0 0 / 26%);
            }
        }
    </style>
</head>
<body>
    <div class="announcement-shell">
        <main class="announcement-page">
            <div class="announcement-hero">
                <img src="assets/hero_background.png" alt="Calamus Education announcement">
            </div>

            <div class="announcement-join-wrap">
                <button type="button" class="announcement-join" id="joinBtn">
                    <span class="announcement-join-icon" aria-hidden="true">→</span>
                    <span>Join Now</span>
                </button>
            </div>
        </main>
    </div>

    <script>
        const joinLink = <?php echo json_encode($joinLink, JSON_UNESCAPED_SLASHES); ?>;

        function openInBrowser(link) {
            window.location = link;
            AndroidInterface.openBrowser(link);
        }

        document.getElementById('joinBtn').addEventListener('click', function () {
            openInBrowser(joinLink);
        });
    </script>
</body>
</html>
