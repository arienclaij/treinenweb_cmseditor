<?php
// --- Functie om metatags te halen ---
function fetch_meta_data($url) {
    $context = stream_context_create([
        'http' => ['user_agent' => 'Mozilla/5.0']
    ]);
    $html = @file_get_contents($url, false, $context);
    if (!$html) return null;

    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $xpath = new DOMXPath($doc);

    $metaData = [
        'title' => '',
        'description' => '',
        'image' => '',
        'site_name' => '',
        'url' => $url,
    ];

    // Open Graph tags
    foreach ($xpath->query('//meta[starts-with(@property, "og:")]') as $meta) {
        $property = $meta->getAttribute('property');
        $content = $meta->getAttribute('content');
        switch ($property) {
            case 'og:title': $metaData['title'] = $content; break;
            case 'og:description': $metaData['description'] = $content; break;
            case 'og:image': $metaData['image'] = $content; break;
            case 'og:site_name': $metaData['site_name'] = $content; break;
        }
    }

    // Twitter Cards
    foreach ($xpath->query('//meta[starts-with(@name, "twitter:")]') as $meta) {
        $name = $meta->getAttribute('name');
        $content = $meta->getAttribute('content');
        switch ($name) {
            case 'twitter:title': if (!$metaData['title']) $metaData['title'] = $content; break;
            case 'twitter:description': if (!$metaData['description']) $metaData['description'] = $content; break;
            case 'twitter:image': if (!$metaData['image']) $metaData['image'] = $content; break;
        }
    }

    // Schema.org JSON-LD
    foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
        $json = json_decode($script->nodeValue, true);
        if (isset($json['@type']) && in_array($json['@type'], ['Article', 'NewsArticle', 'WebPage'])) {
            if (!empty($json['headline'])) $metaData['title'] = $metaData['title'] ?: $json['headline'];
            if (!empty($json['description'])) $metaData['description'] = $metaData['description'] ?: $json['description'];
            if (!empty($json['image'])) {
                if (is_array($json['image'])) $metaData['image'] = $metaData['image'] ?: $json['image'][0];
                else $metaData['image'] = $metaData['image'] ?: $json['image'];
            }
        }
    }

    // Fallback: <title> en description
    if (empty($metaData['title'])) {
        $titleNodes = $xpath->query('//title');
        if ($titleNodes->length > 0) {
            $metaData['title'] = $titleNodes->item(0)->textContent;
        }
    }
    if (empty($metaData['description'])) {
        $descNodes = $xpath->query('//meta[@name="description"]');
        if ($descNodes->length > 0) {
            $metaData['description'] = $descNodes->item(0)->getAttribute('content');
        }
    }

    return $metaData;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Link Preview Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 2rem; }
        .preview-card img { object-fit: cover; height: 200px; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">🔗 Link Preview Generator</h1>
    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="url" name="url" class="form-control" placeholder="Voer een URL in, bv. https://www.nu.nl" required value="<?= htmlspecialchars($_GET['url'] ?? '') ?>">
            <button class="btn btn-primary" type="submit">Ophalen</button>
        </div>
    </form>

    <?php
    if (!empty($_GET['url'])) {
        $meta = fetch_meta_data($_GET['url']);
        if ($meta):
            ?>
            <div class="card preview-card shadow-sm mx-auto" style="max-width: 600px;">
                <?php if ($meta['image']): ?>
                    <img src="<?= htmlspecialchars($meta['image']) ?>" class="card-img-top" alt="Preview image">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($meta['title'] ?: 'Geen titel gevonden') ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($meta['description'] ?: 'Geen beschrijving gevonden') ?></p>
                    <a href="<?= htmlspecialchars($meta['url']) ?>" target="_blank" class="small text-decoration-none text-primary">
                        <?= htmlspecialchars(parse_url($meta['url'], PHP_URL_HOST)) ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Geen metadata gevonden voor deze URL.</div>
        <?php endif; } ?>
</div>
</body>
</html>