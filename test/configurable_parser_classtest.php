<?php

class CMSEmbedParser
{
    private array $handlers = [];
    private array $config = [];

    /**
     * Registreer een handler voor een specifieke tag
     *
     * @param string $tagName De tag naam zonder 'cms-' prefix (bijv. 'image', 'gallery')
     * @param callable $callback Callback functie die de embed HTML returnt
     * @param array $metadata Optionele metadata (name, description, attributes, example)
     */
    public function register(string $tagName, callable $callback, array $metadata = []): self
    {
        $this->handlers[$tagName] = $callback;
        $this->config[$tagName] = $metadata;
        return $this;
    }

    /**
     * Laad configuratie uit een array
     *
     * @param array $config Configuratie array met tag definities
     */
    public function loadConfig(array $config): self
    {
        foreach ($config as $tagName => $definition) {
            if (isset($definition['callback'])) {
                $callback = $definition['callback'];
                unset($definition['callback']);
                $this->register($tagName, $callback, $definition);
            }
        }
        return $this;
    }

    /**
     * Haal alle geregistreerde tags op
     *
     * @return array Array met tag configuraties
     */
    public function getRegisteredTags(): array
    {
        return $this->config;
    }

    /**
     * Genereer documentatie HTML voor alle tags
     *
     * @return string HTML documentatie
     */
    public function generateDocumentation(): string
    {
        $html = '<div class="cms-tags-documentation">';
        $html .= '<h1>CMS Embed Tags Documentatie</h1>';

        foreach ($this->config as $tagName => $definition) {
            $html .= '<div class="tag-doc">';
            $html .= '<h2>' . htmlspecialchars($definition['name'] ?? $tagName) . '</h2>';

            if (isset($definition['description'])) {
                $html .= '<p class="description">' . htmlspecialchars($definition['description']) . '</p>';
            }

            // Attributes
            if (isset($definition['attributes']) && !empty($definition['attributes'])) {
                $html .= '<h3>Attributes:</h3>';
                $html .= '<table class="attributes-table">';
                $html .= '<tr><th>Naam</th><th>Type</th><th>Verplicht</th><th>Beschrijving</th></tr>';

                foreach ($definition['attributes'] as $attrName => $attrInfo) {
                    $required = ($attrInfo['required'] ?? false) ? 'Ja' : 'Nee';
                    $html .= sprintf(
                        '<tr><td><code>data-%s</code></td><td>%s</td><td>%s</td><td>%s</td></tr>',
                        htmlspecialchars($attrName),
                        htmlspecialchars($attrInfo['type'] ?? 'string'),
                        $required,
                        htmlspecialchars($attrInfo['description'] ?? '')
                    );

                    if (isset($attrInfo['options'])) {
                        $html .= sprintf(
                            '<tr><td colspan="4"><em>Opties: %s</em></td></tr>',
                            htmlspecialchars(implode(', ', $attrInfo['options']))
                        );
                    }
                }

                $html .= '</table>';
            }

            // Example
            if (isset($definition['example'])) {
                $html .= '<h3>Voorbeeld:</h3>';
                $html .= '<pre><code>' . htmlspecialchars($definition['example']) . '</code></pre>';
            }

            $html .= '</div><hr>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Parse de content en vervang alle geregistreerde tags
     *
     * @param string $content De content met CMS tags
     * @return string De geparste content met embeds
     */
    public function parse(string $content): string
    {
        foreach ($this->handlers as $tagName => $callback) {
            $pattern = $this->buildPattern($tagName);

            $content = preg_replace_callback($pattern, function($matches) use ($callback, $tagName) {
                // Extraheer alle data-* attributes
                $attributes = $this->extractAttributes($matches[0]);

                // Roep de callback aan met de attributes
                return call_user_func($callback, $attributes, $tagName);
            }, $content);
        }

        return $content;
    }

    /**
     * Bouw het regex pattern voor een specifieke tag
     * Matcht zowel self-closing tags als tags met sluittag
     */
    private function buildPattern(string $tagName): string
    {
        // Matcht: <cms-tag data-x="y"> OF <cms-tag data-x="y" />
        // EN de eventuele sluittag </cms-tag>
        return '/<cms-' . preg_quote($tagName, '/') . '\s+([^>\/]*)\s*\/?>(?:.*?<\/cms-' . preg_quote($tagName, '/') . '>)?/is';
    }

    /**
     * Extraheer alle data-* attributes uit een tag
     */
    private function extractAttributes(string $tag): array
    {
        $attributes = [];

        // Match alle data-* attributes
        preg_match_all('/data-([a-z-]+)=["\']([^"\']*)["\']/', $tag, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[2];
            $attributes[$key] = $value;
        }

        return $attributes;
    }
}

// ============================================
// GEBRUIKSVOORBEELD
// ============================================

$parser = new CMSEmbedParser();

// Laad de configuratie
$config = require 'configurable_parser_config.php';
$parser->loadConfig($config);

// Test content
$content = '
<h1>Mijn Blog Post</h1>
<p>Kijk naar deze mooie foto:</p>
<cms-image data-image-id="42" />

<p>En hier is een gallery:</p>
<cms-gallery data-gallery-id="5"></cms-gallery>

<p>Check deze YouTube video:</p>
<cms-youtube data-youtube-id="AbCdEfG123"></cms-youtube>

<p>Een interessante tweet:</p>
<cms-x data-x-id="1234567890" />

<p>Of met oEmbed (haalt rijkere data op):</p>
<cms-x-oembed data-x-id="1234567890"></cms-x-oembed>

<p>En een Instagram post:</p>
<cms-instagram data-instagram-id="1234567890"></cms-instagram>

<h2>Gerelateerde artikelen</h2>
<p>Lees ook dit artikel:</p>
<cms-article data-article-id="15" data-display="card"></cms-article>

<p>Of bekijk <cms-article data-article-id="23" data-display="inline" /> voor meer info.</p>
';

// Parse de content
$parsed = $parser->parse($content);

echo $parsed;

// Genereer documentatie
echo "\n\n<!-- DOCUMENTATIE -->\n";
echo $parser->generateDocumentation();