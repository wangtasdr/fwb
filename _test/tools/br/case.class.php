<?php
/**
 * for case running
 *
 * @author bell
 */
class Kiss
{
    public $projroot;
    /**
     * case name
     * @var string
     */
    public $name;

    public $path;

    private $ext;
    /**
     * type of case is core or another
     * @var boolean
     */
    public $is_core;
    /**
     * true means qunit, false means jsspec
     * @var boolean
     */
    public $js_frame;
    /**
     * case id shown in html
     * @var string
     */
    public $case_id;

    /**
     * 某些用例是空的，应该直接过滤掉
     * @var unknown_type
     */
    public $empty = false;

    /**
     *
     * @param string $projroot root of project
     * @param string $name namespace of case
     */
    function __construct( $projroot = '../../../' , $name = 'baidu.core.dom.domUtils' , $ext = '' )
    {

        $this->projroot = $projroot;
        $this->name = $name;

        $this->ext = $ext;
        if ( strlen( $ext ) > 0 ) {
            $ns = explode( '.' , $name );

            $n = array_pop( $ns );
            array_push( $ns , $ext , $n );
            $path = implode( '/' , $ns );
        } else {
            $path = implode( '/' , explode( '.' , $name ) );
        }
//        $dir = explode('/',$path);
//        if($dir[0]=='dialogs')
//            $this->path = $this->projroot . '_test/' . $path . '.html';
//        else
            $this->path = $this->projroot . '_test/' . $path . '.js';
        if ( filesize( $this->path ) < 20 ) {
            $this->empty = true;
            return;
        }
        $this->case_id = 'id_case_' . join( '_' , explode( '.' , $name ) );
    }


    public function print_js( $cov, $release = false )
    {
        print '<script type="text/javascript" src="js/jquery-1.5.1.js"></script>' . "\n";
        print '<script type="text/javascript" src="js/tangram.js"></script>' . "\n";
        print '<script type="text/javascript" src="js/testrunner.js"></script>' . "\n";
        print '<script type="text/javascript" src="js/ext_qunit.js"></script>' . "\n";
        print '<script type="text/javascript" src="js/UserAction.js"></script>' . "\n";
        print '<link media="screen" href="css/qunit.css" type="text/css" rel="stylesheet" />' . "\n";
        print '<link  href="../../../themes/default/_css/ueditor.css" type="text/css" rel="stylesheet" />' . "\n";
        print '<script type="text/javascript" src="js/tools.js"></script>' . "\n";
        print '<script type="text/javascript" charset="utf-8" src="../../editor_config.js"></script>' . "\n";
        print '<script type="text/javascript" charset="utf-8" src="../../../third-party/SyntaxHighlighter/shCore.js"></script>' . "\n";        //                print '<script type="text/javascript" charset="utf-8" src="../../editor_config_src.js"></script>' . "\n";
        print '<script type="text/javascript" charset="utf-8" src="../../../editor_config.js"></script>' . "\n";


        /* load case source*/
        $importurl = "{$this->projroot}_test/tools/br/import.php?f=$this->name";
        if ( $cov ) $importurl .= '^&cov=true';
        print "<script type='text/javascript' src='".$importurl."' ></script>\n";

        /* load case and case dependents*/
        $ps = explode( '.' , $this->name );
        array_pop( $ps );
        array_push( $ps , 'tools' );
        
        if ( file_exists( $this->projroot . '_test/' . implode( '/' , $ps ) . '.js' ) ) //没有就不加载了
            print '<script type="text/javascript" src="' . $this->projroot . '_test/' . implode( '/' , $ps ) . '.js"></script>' . "\n";
        print '<script type="text/javascript" src="' . $this->path . '"></script>' . "\n";


    }

    public function match( $matcher )
    {
        if ( $matcher == '*' )
            return true;
        $len = strlen( $matcher );

        /**
         * 处理多选分支，有一个成功则成功，filter后面参数使用|切割
         * @var unknown_type
         */
        $ms = explode( ',' , $matcher );
        if ( sizeof( $ms ) > 1 ) {
            foreach ( $ms as $matcher1 ) {
                if ( $this->match( $matcher1 ) )
                    return true;
            }
            return false;
        }

        /**
         * 处理反向选择分支
         */
        if ( substr( $matcher , 0 , 1 ) == '!' ) {
            $m = substr( $matcher , 1 );
            if ( substr( $this->name , 0 , strlen( $m ) ) == $m )
                return false;
            return true;
        }

        if ( $len > strlen( $this->name ) ) {
            return false;
        }
        return substr( $this->name , 0 , $len ) == $matcher;
    }

    public static function listcase( $matcher = "*" , $projroot = '../../../' )
    {
        $srcpath = $projroot . '_src/';
        $testpath = $projroot . '_test/';
        require_once 'filehelper.php';
        $caselist = getSameFile( $srcpath , $testpath , '' );
        sort($caselist,SORT_STRING);
        foreach ( $caselist as $caseitem ) {
            /*将文件名替换为域名方式，替换/为.，移除.js*/
            $name = str_replace( '/' , '.' , substr( $caseitem , 0 , -3 ) );
            $c = new Kiss( $projroot , $name );
            if ( $c->empty )
                continue;
            if ( $c->match( $matcher ) ) {
                $newName = explode( '\\.' , $name );
                $newName = $newName[ count( $newName ) - 1 ];
                print( "<a href=\"run.php?case=$name\" id=\"$c->case_id\" class=\"jsframe_qunit\" target=\"_blank\" title=\"$name\" onclick=\"run('$name');\$('#id_rerun').html('$name');return false;\">"
                       /*过长的时候屏蔽超出20的部分，因为隐藏的处理，所有用例不能直接使用标签a中的innerHTML，而应该使用title*/
                       . $newName . "</a>\n" );
            }
        }
    }

    public static function listSrcOnly( $print = true , $projroot = '../../../' )
    {
        $srcpath = $projroot . '_src/';
        $testpath = $projroot . '_test/';
        require_once 'filehelper.php';
        $caselist = getSameFile( $srcpath , $testpath , '' );
        $srclist = getSrcOnlyFile( $srcpath , $testpath , '' );
        $srcList = array();
        foreach ( $srclist as $case ) {
            if ( in_array( $case , $caselist ) )
                continue;
            $name = str_replace( '/' , '.' , substr( $case , 0 , -3 ) );
            $tag = "<a class=\"jsframe_qunit\" title=\"$name\">" . ( strlen( $name ) > 20 ? substr( $name , 6 )
                    : $name ) . "</a>";
            array_push( $srcList , $tag );
            if ( $print )
                echo $tag;
        }
        return $srcList;
    }
}

?>