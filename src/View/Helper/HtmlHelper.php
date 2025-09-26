<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class HtmlHelper extends Helper
{
    public function css($path)
    {
        return '<link rel="stylesheet" href="' . $path . '">';
    }
    
    public function script($path)
    {
        return '<script src="' . $path . '"></script>';
    }
    
    public function link($text, $url, $options = [])
    {
        $class = isset($options['class']) ? ' class="' . $options['class'] . '"' : '';
        return '<a href="' . $url . '"' . $class . '>' . $text . '</a>';
    }
    
    /**
     * Returns a charset META-tag.
     *
     * @param string|null $charset Desired character set. If null, UTF-8 will be used.
     * @return string Formatted META element
     */
    public function charset(?string $charset = null): string
    {
        if ($charset === null) {
            $charset = 'UTF-8';
        }
        
        return '<meta charset="' . $charset . '">';
    }
    
    /**
     * Returns a META tag for various purposes.
     *
     * @param string $type Type of meta tag (icon, description, etc.)
     * @param string|array|null $url URL or options array
     * @param array $options Additional options
     * @return string Formatted META element
     */
    public function meta(string $type, $url = null, array $options = []): string
    {
        if ($type === 'icon') {
            $href = $url ?? '/favicon.ico';
            return '<link rel="icon" href="' . $href . '">';
        }
        
        if ($type === 'description' && is_string($url)) {
            return '<meta name="description" content="' . htmlspecialchars($url) . '">';
        }
        
        // Default meta tag
        return '<meta name="' . $type . '" content="' . htmlspecialchars($url ?? '') . '">';
    }

    /**
     * Returns an IMG tag for the given image path.
     *
     * @param string $path Path to the image
     * @param array $options Additional options for the img tag
     * @return string Formatted IMG element
     */
    public function image(string $path, array $options = []): string
    {
        $alt = isset($options['alt']) ? ' alt="' . htmlspecialchars($options['alt']) . '"' : '';
        $title = isset($options['title']) ? ' title="' . htmlspecialchars($options['title']) . '"' : '';
        $class = isset($options['class']) ? ' class="' . htmlspecialchars($options['class']) . '"' : '';
        $id = isset($options['id']) ? ' id="' . htmlspecialchars($options['id']) . '"' : '';
        
        // Handle plugin paths (e.g., 'DebugKit./img/cake.icon.png')
        if (strpos($path, '.') === 0 || strpos($path, '/') === 0) {
            $src = $path;
        } else {
            $src = '/' . ltrim($path, '/');
        }
        
        return '<img src="' . htmlspecialchars($src) . '"' . $alt . $title . $class . $id . '>';
    }
}