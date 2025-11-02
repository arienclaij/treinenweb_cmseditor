<?php
/**
 * CMS Embed Parser Configuratie
 *
 * Dit bestand bevat alle handler configuraties voor de CMS embed parser.
 * Elke tag definitie bevat metadata en een callback functie.
 */

return [
    // ============================================
    // IMAGE TAG
    // ============================================
    'image' => [
        'name' => 'Image',
        'description' => 'Toont een afbeelding uit de media bibliotheek',
        'attributes' => [
            'image-id' => [
                'required' => true,
                'type' => 'integer',
                'description' => 'Het ID van de afbeelding'
            ]
        ],
        'example' => '<cms-image data-image-id="42" />',
        'callback' => function($attrs) {
            $imageId = $attrs['image-id'] ?? '';

            // Hier zou je normaal de image data uit de database halen
            // Bijvoorbeeld: $image = getImageById($imageId);

            return sprintf(
                '<figure class="cms-image">
                    <img src="/images/%s.jpg" alt="Image %s" loading="lazy">
                </figure>',
                htmlspecialchars($imageId),
                htmlspecialchars($imageId)
            );
        }
    ],

    // ============================================
    // GALLERY TAG
    // ============================================
    'gallery' => [
        'name' => 'Gallery',
        'description' => 'Toont een fotogalerij',
        'attributes' => [
            'gallery-id' => [
                'required' => true,
                'type' => 'integer',
                'description' => 'Het ID van de galerij'
            ]
        ],
        'example' => '<cms-gallery data-gallery-id="5" />',
        'callback' => function($attrs) {
            $galleryId = $attrs['gallery-id'] ?? '';

            return sprintf(
                '<div class="cms-gallery" data-gallery="%s">
                    <div class="gallery-container">Gallery %s wordt hier geladen</div>
                </div>',
                htmlspecialchars($galleryId),
                htmlspecialchars($galleryId)
            );
        }
    ],

    // ============================================
    // YOUTUBE TAG
    // ============================================
    'youtube' => [
        'name' => 'YouTube',
        'description' => 'Embed een YouTube video met oEmbed',
        'attributes' => [
            'youtube-id' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Het YouTube video ID'
            ]
        ],
        'example' => '<cms-youtube data-youtube-id="dQw4w9WgXcQ" />',
        'callback' => function($attrs) {
            $videoId = $attrs['youtube-id'] ?? '';

            if (empty($videoId)) {
                return '<div class="cms-error">Geen YouTube video ID opgegeven</div>';
            }

            $videoUrl = 'https://www.youtube.com/watch?v=' . $videoId;
            $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($videoUrl) . '&format=json';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $oembedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['html'])) {
                    return '<div class="cms-video-wrapper">' . $data['html'] . '</div>';
                }
            }

            // Fallback
            return sprintf(
                '<div class="cms-video-wrapper">
                    <iframe width="560" height="315" 
                        src="https://www.youtube.com/embed/%s" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>',
                htmlspecialchars($videoId)
            );
        }
    ],

    // ============================================
    // X/TWITTER TAG
    // ============================================
    'x' => [
        'name' => 'X / Twitter',
        'description' => 'Embed een X/Twitter post met oEmbed',
        'attributes' => [
            'x-id' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Het tweet/post ID'
            ]
        ],
        'example' => '<cms-x data-x-id="1234567890" />',
        'callback' => function($attrs) {
            $tweetId = $attrs['x-id'] ?? '';

            if (empty($tweetId)) {
                return '<div class="cms-error">Geen tweet ID opgegeven</div>';
            }

            $tweetUrl = 'https://twitter.com/x/status/' . $tweetId;
            $oembedUrl = 'https://publish.twitter.com/oembed?url=' . urlencode($tweetUrl);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $oembedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data['html'])) {
                    return $data['html'];
                }
            }

            // Fallback
            return sprintf(
                '<blockquote class="twitter-tweet">
                    <a href="%s"></a>
                </blockquote>
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>',
                htmlspecialchars($tweetUrl)
            );
        }
    ],

    // ============================================
    // INSTAGRAM TAG
    // ============================================
    'instagram' => [
        'name' => 'Instagram',
        'description' => 'Embed een Instagram post',
        'attributes' => [
            'instagram-id' => [
                'required' => true,
                'type' => 'string',
                'description' => 'Het Instagram post ID'
            ]
        ],
        'example' => '<cms-instagram data-instagram-id="ABC123" />',
        'callback' => function($attrs) {
            $postId = $attrs['instagram-id'] ?? '';

            if (empty($postId)) {
                return '<div class="cms-error">Geen Instagram post ID opgegeven</div>';
            }

            $postUrl = 'https://www.instagram.com/p/' . $postId . '/';

            return sprintf(
                '<blockquote class="instagram-media" data-instgrm-permalink="%s" data-instgrm-version="14">
                </blockquote>
                <script async src="//www.instagram.com/embed.js"></script>',
                htmlspecialchars($postUrl)
            );
        }
    ],

    // ============================================
    // ARTICLE TAG
    // ============================================
    'article' => [
        'name' => 'Article',
        'description' => 'Toont een artikel referentie met titel en afbeelding',
        'attributes' => [
            'article-id' => [
                'required' => true,
                'type' => 'integer',
                'description' => 'Het ID van het artikel'
            ],
            'display' => [
                'required' => false,
                'type' => 'string',
                'description' => 'Display mode: card (default), inline, title-only, thumbnail',
                'default' => 'card',
                'options' => ['card', 'inline', 'title-only', 'thumbnail']
            ]
        ],
        'example' => '<cms-article data-article-id="15" data-display="card" />',
        'callback' => function($attrs) {
            $articleId = $attrs['article-id'] ?? '';
            $display = $attrs['display'] ?? 'card';

            if (empty($articleId)) {
                return '<div class="cms-error">Geen artikel ID opgegeven</div>';
            }

            // Dummy artikelen voor demo
            $articles = [
                '15' => [
                    'title' => 'Nieuwe functionaliteiten in PHP 8.3',
                    'image' => '/uploads/news/php-83.jpg',
                    'excerpt' => 'PHP 8.3 brengt interessante nieuwe features zoals typed class constants en meer.',
                    'slug' => 'nieuwe-functionaliteiten-php-83'
                ],
                '23' => [
                    'title' => 'Tips voor betere database performance',
                    'image' => '/uploads/news/database-tips.jpg',
                    'excerpt' => 'Ontdek hoe je jouw database queries kunt optimaliseren voor betere prestaties.',
                    'slug' => 'tips-database-performance'
                ],
                '42' => [
                    'title' => 'Webdesign trends 2025',
                    'image' => '/uploads/news/design-trends.jpg',
                    'excerpt' => 'De nieuwste trends in webdesign die je dit jaar moet kennen.',
                    'slug' => 'webdesign-trends-2025'
                ]
            ];

            $article = $articles[$articleId] ?? null;

            if (!$article) {
                return '<div class="cms-error">Artikel niet gevonden</div>';
            }

            $article['url'] = '/nieuws/' . $article['slug'];

            switch($display) {
                case 'card':
                    return sprintf(
                        '<article class="cms-article-card">
                            <a href="%s">
                                <img src="%s" alt="%s" loading="lazy">
                                <h3>%s</h3>
                                <p>%s</p>
                                <span class="read-more">Lees verder →</span>
                            </a>
                        </article>',
                        htmlspecialchars($article['url']),
                        htmlspecialchars($article['image']),
                        htmlspecialchars($article['title']),
                        htmlspecialchars($article['title']),
                        htmlspecialchars($article['excerpt'])
                    );

                case 'inline':
                    return sprintf(
                        '<a href="%s" class="cms-article-inline">%s</a>',
                        htmlspecialchars($article['url']),
                        htmlspecialchars($article['title'])
                    );

                case 'title-only':
                    return sprintf(
                        '<span class="cms-article-title">%s</span>',
                        htmlspecialchars($article['title'])
                    );

                case 'thumbnail':
                    return sprintf(
                        '<a href="%s" class="cms-article-thumb">
                            <img src="%s" alt="%s" loading="lazy">
                            <span>%s</span>
                        </a>',
                        htmlspecialchars($article['url']),
                        htmlspecialchars($article['image']),
                        htmlspecialchars($article['title']),
                        htmlspecialchars($article['title'])
                    );

                default:
                    return '';
            }
        }
    ]
];