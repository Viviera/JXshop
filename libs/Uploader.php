<?php
namespace libs;
class Uploader{
    //不允许被new 来生成对象
    private function __construct(){}
    //不允许克隆
    private function __clone(){}
    private static $_obj = null;

    public static function make()
    {
        if(self::$_obj ===null){
            //生成一个对象
            self::$_obj = new self;
        }
        return self::$_obj;
    }
    private $_root = ROOT . 'public/uploads/';
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];  // 允许上传的扩展名
    private $_maxSize = 1024*1024*1.8;

    private $_file;
    private $_subDir;
    // 上传图片
    // 参数一、表单中文件名
    // 参数二、保存到的二级目录
    public function upload($name, $subdir)
    {
        // 把用户图片的信息保存到属性上
        $this->_file = $_FILES[$name];
        $this->_subDir = $subdir;

        if(!$this->_checkType())
        {
            die('图片类型不正确！');
        }

        if(!$this->_checkSize())
        {
            die('图片尺寸不正确！');
        }

        // 创建目录
        $dir = $this->_makeDir();

        // 生成唯一的名字
        $name = $this->_makeName();

        // 移动图片
        move_uploaded_file($this->_file['tmp_name'], $this->_root.$dir.$name);

        // 返回二级目录开始的路径
        return $dir.$name;    // avatar/20180809/1.png
    }

    // 创建目录
    private function _makeDir()
    {
        $dir = $this->_subDir . '/' . date('Ymd');
        if(!is_dir($this->_root . $dir))
            mkdir($this->_root . $dir, 0777, TRUE);  // 循环创建所有目录及子目录

        return $dir.'/';
    }

    // 生成唯一的名字
    private function _makeName()
    {
        $name = md5( time() . rand(1,9999) );
        $ext = strrchr($this->_file['name'], '.');
        return $name . $ext;
    }

    private function _checkType()
    {
        return in_array($this->_file['type'], $this->_ext);
    }

    private function _checkSize()
    {
        return $this->_file['size'] < $this->_maxSize;
    }

    }

