<?php
/**
 * class of page
 *
 * @author assnr <ycassnr@gmail.com>
 */
class Page {

    protected $totalRows;

    protected $listRows;

    protected $firstRows;

    protected $totalPages;    

    protected $nowPage;

    protected $rollPage = 9;

    protected $url;

    protected $p = 'page';

    protected $config = array(
            'header' => '<span class="rows">ﾃ･窶ｦﾂｱ %TOTAL_ROW% ﾃｦﾂ敖｡ﾃｨﾂｮﾂｰﾃ･ﾂｽ窶｢</span>',
            'prev'   => '<',
            'next'   => '>',
            'first'  => 'ﾃｨﾂｵﾂｷﾃｧ窶堋ｹ',
            'last'   => 'ﾃｧﾂｻﾋｧ窶堋ｹ',
            'theme'  => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%',
        );

    public function __construct($totalRows, $listRows, $urlParams = '')
    {
        $this->totalRows = $totalRows;

        $this->listRows = $listRows;
        $this->nowPage = (empty($_GET[$this->p]) || $_GET[$this->p] < 1) ? 1 : intval($_GET[$this->p]);

        $this->totalPages = ceil($this->totalRows/$this->listRows);

        if ($this->nowPage > $this->totalPages && $this->totalPages > 0)
            $this->nowPage = $this->totalPages;

        $this->firstRows = ($this->nowPage - 1) * $this->listRows;

        $this->url = $this->initalizationUrl($urlParams);

    }

    /**
     * set param value.
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        return property_exists($this, $name) ? $this->$name = $value : '';
    }

    
    /**
     * set param config values.
     *
     * @return mixed
     */
    public function setConfig($key, $value)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] = $value : '';
    }

    /**
     * init url
     *
     * @return string
     */
    protected function initalizationUrl($param)
    {
        $url = $_SERVER['REQUEST_URI'];

        $parseArr = parse_url($url);

        !empty($parseArr['query']) ? parse_str($parseArr['query'], $query) : $query = '';

        if (is_string($param))
            parse_str($param, $param);

        $param[$this->p] = '[PAGE]';

        $parseArr['query'] = http_build_query($query ? array_merge($query, $param) : $param);

        return is_callable('http_build_url') ? http_build_url($parseArr) : $parseArr['path'] . '?' . $parseArr['query'];
    }

    /**
     * generate url.
     *
     * @return string
     */
    protected function generationUrl($page)
    {
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * data limit
     *
     */
    public function limit()
    {
        return $this->firstRows . ',' . $this->listRows;

    }

    public function pageInfo()
    {
        return array(
                'totalRows' => $this->totalRows,
                'nowPage' => (empty($_GET[$this->p]) || $_GET[$this->p] < 1) ? 1 : intval($_GET[$this->p]),
                'totalPages' => $this->totalPages,
            );

    }

    /**
     * show page html data.
     *
     * @return string
     */
    public function show()
    {
        $pageStr = '';
        if ($this->totalRows == 0)        
            return '';

        // firstPage
        $firstPage = '';
        if ($this->nowPage != 1)
            $firstPage = '<a title="ﾃｩﾂｦ窶禿ｩﾂ｡ﾂｵ" href="' . $this->generationUrl(1) . '" class="first">' . $this->config['first'] . '</a>';

        // up page
        $upPage = '';
        if ($this->nowPage > 1)
            $upPage = '<a title="ﾃ､ﾂｸﾅﾃ､ﾂｸ竄ｬﾃｩﾂ｡ﾂｵ" href="' . $this->generationUrl($this->nowPage - 1) . '" class="prev">'. $this->config['prev']  . '</a>';

        // next page
        $nextPage = '';
        if ($this->nowPage < $this->totalPages)
            $nextPage = '<a title="ﾃ､ﾂｸ窶ｹﾃ､ﾂｸ竄ｬﾃｩﾂ｡ﾂｵ" href="' . $this->generationUrl($this->nowPage + 1) . '" class="next">'. $this->config['next']  . '</a>';

        // end page
        $endPage = '';
        if ($this->nowPage != $this->totalPages)
            $endPage = '<a title="ﾃｧ窶堋ｹﾃ･ﾂｼ竄ｬﾃｦﾅ凪ｰﾃｦﾆ椎ﾃ･窶毒 href="' . $this->generationUrl($this->totalPages) . '" class="end">'. $this->config['last']  . '</a>';

        $halfRollPage = $this->rollPage/2;
        $halfRollPageCeil = ceil($halfRollPage);
        $page = 0;
        $linkPage = '';

        $lellipsis = $rellipsis = '<span class="ellipsis">...</span>';
        if ($this->totalPages <= $this->rollPage)
            $lellipsis = $rellipsis = '';

        for ($i = 1; $i <= $this->rollPage; $i ++) :
            if ($this->nowPage <= $halfRollPageCeil) :
                $page = $i;
                $lellipsis = '';
            elseif ($this->nowPage + $halfRollPageCeil - 1 >= $this->totalPages) :
                $page = $this->totalPages - $this->rollPage  + $i;
                $rellipsis = '';
            else :
                $page = $this->nowPage - $halfRollPageCeil + $i;
            endif;

            if ($page > 0 && $page != $this->nowPage) :
                if ($page < $this->totalPages)
                    $linkPage .= '<a href="' . $this->generationUrl($page) . '" class="num">' . $page . '</a>';
                else
                    break;
            else :
                if ($page > 0)
                    $linkPage .= '<span class="current">' . $page . '</span>';
            endif;
        endfor;
        $linkPage = $lellipsis . $linkPage . $rellipsis;

        $pageStr = str_replace(
            array(
                '%HEADER%', 
                '%NOW_PAGE%',
                '%UP_PAGE%', 
                '%DOWN_PAGE%', 
                '%FIRST%', 
                '%LINK_PAGE%', 
                '%END%',
                '%TOTAL_ROW%', 
                '%TOTAL_PAGE%'
            ),
            array(
                $this->config['header'],
                $this->nowPage,
                $upPage,
                $nextPage,
                $firstPage,
                $linkPage,
                $endPage,
                $this->totalRows,
                $this->totalPages
            ),
            $this->config['theme']);

        return '<div id="page"><div class="tab-pagging-nav">' . $pageStr . '</div></div>';
    }

}
