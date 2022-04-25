<?php

class base {
	public function __construct($checkLogin = false) {
        if ($checkLogin) {
            $this->checkLogin();
        }
	}

	protected function checkLogin() {
	    if (empty($_SESSION[SessionConst::UserId])) {
	        $this->redirect('/admin/auth/login');
        }
    }

	protected function redirect($url, $status = 302) {
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
		exit();
	}

	/**
	 * 如果以后要采用Theme，那么就可以考虑重写本方法
	 */
	private function getViewFile($view) {
		return dirname(dirname(__FILE__)) . '/views/' . $view . '.php';
	}

	protected function renderFile($file, $renderData, $return = false) {
		if (file_exists($file)) {
			extract($renderData, EXTR_OVERWRITE);

			ob_start();
			ob_implicit_flush(0);
			require($file);
			$content =  ob_get_clean();
				
			if ($return) {
				return $content;
			} else {
				echo $content;
			}

		} else {
			throw new Exception("Can not found $file in controller $this->route");
		}
	}

	/**
	 * 不渲染layout的render方法
	 *
	 * @param array $renderData
	 * @param string $view
	 * @param boolean $return
	 * @return string
	 */
	protected function renderWithoutLayout($renderData = array(), $view = null, $return = false) {
		return $this->render($renderData, $view, $return, false);
	}

	/**
	 * 渲染视图的方法
	 *
	 * @param  array $renderData 用于渲染的视图层的数据
	 * @param  string $view 视图层的模板，如果没有，就根据控制器当前的route来寻找
	 * @param  boolean $return 是否返回内容，而不是直接输出，如果需要再控制内部再做一些处理，比如静态化等，需要这个
	 * @param  boolean|string $layout 选择模板用什么layout文件，false表示不需要layout；null表示用当前的route；也可以指定一个layout
	 * @return string
	 */
	protected function render($renderData = array(), $view = null, $return = false, $layout = null) {
		$viewFile = $this->getViewFile($view);
		$output = $this->renderFile($viewFile, $renderData, true);

		if($return) {
			return $output;
		} else {
			echo $output;
		}
	}
	
	/**
	 * 渲染输出json格式的内容
	 * @param array $data
	 */
	protected function renderJSON($data) {
		header('Content-type: application/json');
		echo json_encode($data);
	}

    /**
     * 设置分页
     *
     * @param int $page
     * @param int $per
     * @param $total
     * @param $base_url
     * @param null $ajax_function
     * @param bool $get_method
     * @return string
     */
    protected function setPage($page = 1, $per = 10, $total, $base_url) {
        $page = intval($page);
        $page = $page == 0 ? 1 : $page;
        $base_url = rtrim($base_url, '/');
        if ( ($page - 1) * $per > $total ) {
            $this->redirect( $base_url );
        }

        if (strpos($base_url, '?') !== false) {
            $url = $base_url . '&page=';
        } else {
            $url = $base_url . '?page=';
        }

        $obj = new Page(array(
            'total'     => $total,
            'perpage'   => $per,
            'nowindex'  => $page,
            'url'       => $url,
        ), true);

        $obj->current_class = 'current';
        return $obj->show(6);
    }
}