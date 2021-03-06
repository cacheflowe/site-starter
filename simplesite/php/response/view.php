<?php
class View {
  function __construct() {
    $this->htmlStr = '';
    $this->buildHtmlData();
  }

	function html() { return $this->htmlStr . $this->pageJsFix(); }

  function pageJsFix() {
    global $request;
    if($request->isAPI()) {
      return "";
    } else {
      return "<script>if(document.querySelector('head meta') == null) document.location.reload();</script>";
    }
  }

  function buildHtmlData() {
		// get path array and decide how to include the proper ajax page
		global $request;
		global $metadata;

    // set locations
		$dataRoot = './data/rss/';
		$pagesRoot = './php/views/';
    // get basic path
		$path = $request->path();

		$pathParams = '';
    $pathComponents = $request->pathComponents();
    $pathComponentsCopy = explode( '/', substr( $path, 1 ) );

    // init empty values
    $xmlFile = '';
    $includeFile = '';
    $htmlStr = '';

    // helper vars to check paths
    $foundPage = false;
    $numPathComponents = count($pathComponentsCopy);
    $pathLastIndex = $numPathComponents - 1;
    $pathLastIndexOrig = $numPathComponents;
    $checkPagePath = '';

    // check all combinations of the path, looking for a match. send the rest along as params
    // i.e.: search for /collection/one/param then /collection/one then /collection
    while ($pathLastIndex >= 0 && $foundPage == false) {
      // transform path to dashed filename
      $dashedFilePath = implode("-", $pathComponentsCopy);
      // and look for matching php or xml file
      $checkPagePath = $pagesRoot . $dashedFilePath . '.php';
      $checkDataPath = $dataRoot . $dashedFilePath . '.xml';

      // did we find a file?
      if( file_exists($checkPagePath) == true ) {
        // re-assemble path based on found static php view file
        $foundPage = true;
        $pathArray = array_slice($pathComponents, 0, $pathLastIndex + 1);
        $path = "/" . implode("/", $pathArray);

        // send the rest of the path back as params
        $paramsArray = array_slice($pathComponents, $pathLastIndex + 1, $numPathComponents - $pathLastIndex + 1);
    		$pathParams = implode("/", $paramsArray);

        // NOW INCLUDE THIS PAGE - fix this up
        $includeFile = $checkPagePath;
      }
      else if( file_exists($checkDataPath) == true ) {
        // re-assemble path based on found rss file
        $foundPage = true;
        $pathArray = array_slice($pathComponents, 0, $pathLastIndex + 1);
        $path = "/" . implode("/", $pathArray);

        // send the rest of the path back as params
        $paramsArray = array_slice($pathComponents, $pathLastIndex + 1, $numPathComponents - $pathLastIndex + 1);
    		$pathParams = implode("/", $paramsArray);

        // do the same type of thing here
        $xmlFile = $dataRoot . implode("-", $pathComponents) . ".xml";
        $newsListingView = new NewsListingView($checkDataPath, $path, $pathParams, $pathComponents);
        $squarePreviewClass = (strpos( $path, '/music/discography' ) !== false || strpos( $path, '/art/' ) !== false || strpos( $path, '/store' ) !== false) ? ' class="content-square-previews"' : '';  // add special class for square previews
        $htmlStr = '<div'.$squarePreviewClass.' data-page-title="' . $metadata->get_pageTitle() . '">' . $newsListingView->html . '</div>';

      }
      // move on to next path attempt
      array_pop($pathComponentsCopy);

      // keep checking path depth
      $pathLastIndex--;
    }

    // nothing found: 404
    if($foundPage == false) {
      $includeFile = $pagesRoot . '404.php';
    }

    // pull in include file if there is one
		if($includeFile != '') {
			ob_start();
			include $includeFile;
			$htmlStr = ob_get_clean();
		}

    // needs auth
    if($request->needsAuth() && Login::isLoggedIn() == false && $pathComponents[0] != 'login') {
      ob_start();
			include $pagesRoot . 'login.php';
			$htmlStr = ob_get_clean();
    }

		$this->htmlStr = $htmlStr;
  }

  function __destruct() {
    $this->htmlStr = null;
  }
}
?>
