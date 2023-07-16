<?php

namespace MediaWiki\Extension\PkgStore;

use ConfigException;
use MWException;
use OutputPage, Parser, Skin;

/**
 * Class MW_EXT_Comments
 */
class MW_EXT_Comments
{
  /**
   * Register tag function.
   *
   * @param Parser $parser
   *
   * @return void
   * @throws MWException
   */
  public static function onParserFirstCallInit(Parser $parser): void
  {
    $parser->setFunctionHook('comments', [__CLASS__, 'onRenderTag']);
  }

  /**
   * Render tag function.
   *
   * @param Parser $parser
   * @param string $type
   * @param string $id
   *
   * @return string|null
   * @throws MWException
   */
  public static function onRenderTag(Parser $parser, string $type = '', string $id = ''): ?string
  {
    // Argument: type.
    $getType = MW_EXT_Kernel::outClear($type ?? '' ?: '');

    // Argument: id.
    $getID = MW_EXT_Kernel::outClear($id ?? '' ?: '');

    // Check page status.
    if (!MW_EXT_Kernel::getTitle() || !MW_EXT_Kernel::getTitle()->isContentPage() || !MW_EXT_Kernel::getWikiPage()) {
      return null;
    }

    switch ($getType) {
      case 'disqus':
        // Build data.
        $siteURL = MW_EXT_Kernel::getConfig('Server');
        $pageURL = $siteURL . '/?curid=' . MW_EXT_Kernel::getTitle()->getArticleID();
        $pageID = MW_EXT_Kernel::getTitle()->getArticleID();

        // Out type.
        $outType = '<div id="disqus_thread"></div>';
        $outType .= '<script>let disqus_config = function () { this.page.url = "' . $pageURL . '"; this.page.identifier = "' . $pageID . '"; };</script>';
        $outType .= '<script>(function() { let d = document, s = d.createElement("script"); s.src = "https://' . $getID . '.disqus.com/embed.js"; s.setAttribute("data-timestamp", +new Date()); (d.head || d.body).appendChild(s); })();</script>';
        break;
      case 'facebook':
        $outType = '<div id="mw-comments-fb" class="fb-comments" data-href="https://developers.facebook.com/docs/plugins/comments#configurator" data-numposts="5"></div>';
        break;
      case 'vk':
        // Build data.
        $siteURL = MW_EXT_Kernel::getConfig('Server');
        $pageURL = $siteURL . '/?curid=' . MW_EXT_Kernel::getTitle()->getArticleID();
        $pageID = MW_EXT_Kernel::getTitle()->getArticleID();

        // Out type.
        $outType = '<script>VK.init({apiId: ' . $getID . ', onlyWidgets: true});</script>';
        $outType .= '<div id="mw-comments-vk"></div>';
        $outType .= '<script>VK.Widgets.Comments("mw-comments-vk", {limit: 15, attach: "*", pageUrl: "' . $pageURL . '"});</script>';
        break;
      default:
        $parser->addTrackingCategory('mw-comments-error-category');

        return null;
    }

    // Out HTML.
    $outHTML = '<div class="mw-comments navigation-not-searchable">' . $outType . '</div>';

    // Out parser.
    return $parser->insertStripItem($outHTML, $parser->getStripState());
  }

  /**
   * Load resource function.
   *
   * @param OutputPage $out
   * @param Skin $skin
   *
   * @return void|null
   * @throws MWException
   */
  public static function onBeforePageDisplay(OutputPage $out, Skin $skin)
  {
    if (!MW_EXT_Kernel::getTitle() || !MW_EXT_Kernel::getTitle()->isContentPage() || !MW_EXT_Kernel::getWikiPage()) {
      return null;
    }

    $out->addHeadItem('mw-comments-vk', '<script src="https://vk.com/js/api/openapi.js"></script>');
    $out->addModuleStyles(['ext.mw.comments.styles']);
  }
}
