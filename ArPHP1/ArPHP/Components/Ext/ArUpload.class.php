<?php
class ArUpload extends ArComponent {

    public $dest = '';

    public $source = '';

    public $errorMsg = null;

    protected $upFiled = '';

    public function errorMsg()
    {
        return $this->errorMsg;

    }

    static public $mimeMap = array(

            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',

        );

    public function upload($upFiled, $dest = '', $extension = 'all')
    {
        $this->upFiled = $upFiled;

        if (empty($_FILES[$this->upFiled]) || is_uploaded_file($_FILES[$this->upFiled]['tmp_name'])) :
            if ($extension == 'all' || $this->checkFileType($_FILES[$this->upFiled]['type'], $extension)) :
                $dest = empty($dest) ? arCfg('PATH.UPLOAD') : $dest;

                if (!is_dir($dest)) :
                    mkdir($dest);
                endif;

                $upFileName = $this->generateFileName();
                $destFile = rtrim($dest, DS) . DS . $upFileName;

                if (move_uploaded_file($_FILES[$this->upFiled]['tmp_name'], $destFile)) :

                else :
                    $this->errorMsg = '上传出错';
                endif;

            endif;

        else :
            $this->errorMsg = "Filed '$upFiled' invalid";
        endif;

        if (!!$this->errorMsg) :
            return false;
        else :
            return $upFileName;
        endif;

    }

    protected function checkFileType($fileType, $extension)
    {
        if ($extension == 'img') :
            if (!in_array($fileType, array(self::$mimeMap['jpg'], self::$mimeMap['png'], self::$mimeMap['gif']))) :
                $this->errorMsg = "仅支持图片类型";
            endif;
        elseif (empty(self::$mimeMap[$extension])) :

            $this->errorMsg = ".{$extension}不支持的上传类型,支持的类型:" . implode(',', self::$mimeMap);

        else :
            if ($fileType != self::$mimeMap[$extension]) :
                $this->errorMsg ="仅支持.{$extension}类型";
            endif;
        endif;

        return !$this->errorMsg;

    }

    protected function generateFileName()
    {
        return md5(time() . rand()) . '.' . substr($_FILES[$this->upFiled]['name'], strrpos($_FILES[$this->upFiled]['name'], '.') + 1);

    }

}
