<?php
/**
 * Created by PhpStorm.
 * User: ATA
 * Date: 2/7/2018
 * Time: 8:28 PM
 */


/**
 * Class B_var_dump
 *
 * Can be used instead of "var_dump" function in PHP and show variables
 * in visualized and user-friendly style
 *
 * @version 1.0
 * @author Ata Sajedi ata_sajedi@yahoo.com
 * $source
 */
class BeautifulVarDump
{
    // Variable passed to class
    public static $inlet;
    // Type of inlet variable
    public static $type;
    // Content created for printing
    public static $content;
    // Depth of array & object inlets
    public static $depth = 1;
    // Store arrays & objects for looping and showing them in nested div
    public static $temp = [];
    // An instance of class
    public static $instance;

    /**
     * Create an instance of the class
     *
     * @return BeautifulVarDump
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$instance->__construct();

        return self::$instance;
    }

    /**
     * B_var_dump constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $s
     *
     * @use self::build()
     * @use self::do_single()
     *
     * @return string self::$content
     */
    public static function start($s)
    {
        self::$inlet = $s;
        self::$type = gettype($s);

        if (in_array(self::$type, array('array', 'object')))
            return self::build();
        else
            return self::do_single();
    }

    /**
     * Handle inlet when it isn't "array" or "object"
     *
     * @return string self::$content
     */
    public static function do_single()
    {
        self::$content = '<div class="b_var_dump">
                            <p>Type: ' . self::$type . '</p>
                            <div>
                                <p class="bvp_value">' . self::$inlet . '</p>
                            </div>
                          </div>';

        return self::$content;
    }

    /**
     * Handle inlet when it is "array" or "object"
     *
     * @use self::do_loop()
     * @use self::do_switch()
     *
     * @return string self::$content
     */
    public static function build()
    {
        self::$content = '<div class="b_var_dump">';
        self::$content .= '<p>Type: ' . self::$type . '</p>';

        foreach (self::$inlet as $k => $v) {

            self::$depth = 1;

            self::$content .= '<div class="bvd_first_row">';
            self::$content .= '<div>';
            self::$content .= '<p>"' . $k . '" -- (' . gettype($v) . ')</p>';

            self::$content .= self::do_switch($v);

            self::$content .= '</div>';
            self::$content .= '</div>';
        }

        self::$content .= '</div>';

        if (count(self::$temp) > 0) {
            self::do_loop();
        }

        return self::$content;
    }

    /**
     * Loop through "self::$temp" and replace nested elements with their content in "self::$content"
     *
     * @use self::replace_with()
     *
     * @return void
     */
    public static function do_loop()
    {
        // Placeholder for the real $temp array. Otherwise depth would be missed up
        $ph = self::$temp;
        self::$depth = 2;
        $count = count($ph);
        reset($ph);

        for ($i = 0; $i < $count; ++$i) {

            self::replace_with(key($ph), current($ph));

            // Get ready for next depth at the end of current loop
            if ($i == ($count - 1) && count(self::$temp) > 0) {
                $ph = self::$temp;
                reset($ph);
                $i = $count - count(self::$temp) - 1;
                // Increase depth level by level
                self::$depth = self::$depth + 1;
            } else {
                // If loop is not ended, go to next index in current loop
                next($ph);
            }
        }
    }

    /**
     * Print element when input isn't array or object; otherwise store new index in "self::$temp"
     *
     * @param $v
     * @return string
     */
    public static function do_switch($v)
    {
        $con = '';

        if ($v == '' || (is_array($v) && count($v) < 1)) {
            $vp = '""';
        } else {
            $vp = $v;
        }

        switch (gettype($v)) {
            case 'boolean':
                if ($v)
                    $con .= '<p class="bvp_value">true</p>';
                else
                    $con .= '<p class="bvp_value">false</p>';
                break;
            case 'string':
            case 'integer':
            case 'double':
                $con .= '<p class="bvp_value">' . $vp . '</p>';
                break;
            case 'NULL':
                $con .= '<p class="bvp_value">NULL</p>';
                break;
            case 'array':
            case 'object':
                if (count($v) < 1)
                    $con .= '<p class="bvp_value_empty">empty</p>';
                else {
                    $rand = self::generateRandomString();
                    $con .= '<p>' . $rand . '</p>';
                    self::$temp[$rand] = $v;
                }
                break;
        }

        return $con;
    }

    /**
     * Replace inner elements in the result of "build" function
     *
     * @param $k
     * @param $v
     *
     * @use self::do_switch()
     *
     * @use preg_replace
     */
    public static function replace_with($k, $v)
    {
        $cont = '<div class="bvd_' . self::$depth . '_row">';

        foreach ($v as $k2 => $v2) {

            $cont .= '<div>';
            $cont .= '<p>"' . $k2 . '" -- (' . gettype($v2) . ')</p>';

            $cont .= self::do_switch($v2);

            $cont .= '</div>';
            next($v);
        }
        $cont .= '</div>';

        unset(self::$temp[$k]);

        self::$content = preg_replace('!' . $k . '!', $cont, self::$content);
    }

    /**
     * Generate random string for "self::$temp" key
     *
     * @return string
     */
    public static function generateRandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}



function b_v_d($v)
{
    $bvd_obj = BeautifulVarDump::get_instance();

    echo $bvd_obj::start($v);
    //echo 'sdafdsafgdsagasfdg';
}