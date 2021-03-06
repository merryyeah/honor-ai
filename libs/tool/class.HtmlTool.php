<?php
/**
 * HTML 帮助相关类
 * @author yunliang.huang 20140314
 */
class HtmlTool {

    /**
     * 安全输出html
     *
     * @param string $string
     * @param 是否转义 $translate
     * @param 是否转义html实体 $protected
     * @return string
     */
    public static function outputString($string, $translate = false, $protected = false) {
        if ($protected == true) {
            return htmlspecialchars ( $string );
        } else {
            if ($translate == false) {
                return strtr ( trim ( $string ), array (
                    '"' => '&quot;'
                ) );
            } else {
                return strtr ( trim ( $string ), $translate );
            }
        }
    }

    /**
     * 生产一个Form表单
     *
     * @param string $name
     * @param string $action
     * @param string $method
     * @param string $parameters
     * @return string
     */
    public static function drawForm($name, $action, $method = 'post', $parameters = '') {
        $form = '<form name="' . self::outputString ( $name ) . '" action="' . self::outputString ( $action ) . '" method="' . self::outputString ( $method ) . '"';
        if (! empty ( $parameters )) {
            $form .= ' ' . $parameters;
        }
        $form .= '>';
        return $form;
    }

    /**
     * 输出一个input表单
     */
    public static function drawInputField($name, $value = '', $parameters = '', $type = 'text') {
        $field = '<input type="' . self::outputString ( $type ) . '" name="' . self::outputString ( $name ) . '"';
        if (! empty ( $value )) {
            $field .= ' value="' . self::outputString ( $value ) . '"';
        }
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        $field .= ' />';
        return $field;
    }

    /**
     * 输出一个input表单
     */
    public static function drawSubmitField($value = '', $parameters = '', $name = 'submit',  $type = 'submit') {
        $field = '<input type="' . self::outputString ( $type ) . '"';
        if (! empty ( $value )) {
            $field .= ' value="' . self::outputString ( $value ) . '"';
        }
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        if (! empty ( $name )) {
            $field .= ' name="' . $name . '"';
        }
        $field .= ' />';
        return $field;
    }

    /**
     * 绘制一个button按钮
     * @param unknown_type $value
     * @param unknown_type $parameters
     * @param unknown_type $type
     * @return string
     */
    public static function drawButtonField($value = 'Button', $parameters, $type = 'submit'){
        $field = '<button type="' . self::outputString ( $type ) . '"';
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        $field .= '>';
        if (! empty ( $value )) {
            $field .= self::outputString ( $value );
        }
        $field .= '</button>';
        return $field;
    }

    /**
     * 输出一个password表单
     */
    public static function drawPasswordField($name, $value = '', $parameters = 'maxlength="40"') {
        return self::drawInputField ( $name, $value, $parameters, 'password' );
    }

    /**
     * 输出一个selection表单
     */
    private static function dawSelectionField($name, $type, $value = '', $checked = false, $parameters = '') {
        $selection = '<input type="' . self::outputString ( $type ) . '" name="' . self::outputString ( $name ) . '"';
        if (! empty ( $value )) {
            $selection .= ' value="' . self::outputString ( $value ) . '"';
        }
        if ($checked == true) {
            $selection .= ' checked="checked"';
        }
        if (! empty ( $parameters )) {
            $selection .= ' ' . $parameters;
        }
        $selection .= ' />';
        return $selection;
    }

    /**
     * 输出一个checkbox表单
     */
    public static function drawCheckboxField($name, $value = '', $checked = false, $parameters = '') {
        return self::dawSelectionField ( $name, 'checkbox', $value, $checked, $parameters );
    }

    /**
     * 输出一个radio表单
     */
    public static function drawRadioField($name, $value = '', $checked = false, $parameters = '') {
        return self::dawSelectionField ( $name, 'radio', $value, $checked, $parameters );
    }

    /**
     * 输出一个textarea表单
     */
    public static function drawTextareaField($name, $wrap, $width, $height, $text = '', $parameters = '') {
        $field = '<textarea name="' . self::outputString ( $name ) . '" wrap="' . self::outputString ( $wrap ) . '" cols="' . self::outputString ( $width ) . '" rows="' . self::outputString ( $height ) . '"';
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        $field .= '>';
        if (! empty ( $text )) {
            $field .= $text;
        }
        $field .= '</textarea>';
        return $field;
    }

    /**
     * 输出一个hidden表单
     */
    public static function drawHiddenField($name, $value = '', $parameters = '') {
        $field = '<input type="hidden" name="' . self::outputString ( $name ) . '"';
        if (! empty ( $value )) {
            $field .= ' value="' . self::outputString ( $value ) . '"';
        }
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        $field .= ' />';
        return $field;
    }


    /**
     * 生产一个slelect的下拉框
     *
     * @param unknown_type $name
     * @param unknown_type $values
     * @param unknown_type $default
     * @param unknown_type $parameters
     * @return string
     */
    public static function drawSelectFiled($name, $values, $default, $parameters = '') {
        $field = '<select name="' . self::outputString ( $name ) . '"';
        if (! empty ( $parameters )) {
            $field .= ' ' . $parameters;
        }
        $field .= '>' . "\n";
        for($i = 0, $n = sizeof ( $values ); $i < $n; $i ++) {
            $field .= '  <option value="' . self::outputString ( $values [$i] ['id'] ) . '"';
            if(is_array($default) && in_array($values [$i] ['id'] ,$default)) {
                $field .= ' selected="selected"';
            } elseif($default == $values [$i] ['id']) {
                $field .= ' selected="selected"';
            }

            $field .= '>' . self::outputString ( $values [$i] ['text'], array (
                    '"' => '&quot;',
                    '&' => '&',
                    '\'' => '&#039;',
                    '<' => '&lt;',
                    '>' => '&gt;'
                ) ) . '</option>' . "\n";
        }
        $field .= '</select>' . "\n";
        return $field;
    }

    /**
     * 获取协议类型
     * @return string
     */
    public static function getShopProtocol(){
        $protocol = ((! empty ( $_SERVER ['HTTPS'] ) && strtolower ( $_SERVER ['HTTPS'] ) != 'off')) ? 'https://' : 'http://';
        return $protocol;
    }
}