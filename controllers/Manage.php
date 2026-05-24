<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/libraries/Nova_controller_admin.php';

class __extensions__nova_ext_anti_spam_questions__Manage extends Nova_controller_admin
{
	public function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
		$this->ci->load->model('settings_model', 'settings');
		$this->_regions['nav_sub'] = Menu::build('adminsub', 'manageext');
	}

	public function index()
	{
		Auth::check_access('site/settings');

		$action = isset($_POST['action']) ? $_POST['action'] : '';
		$submit = isset($_POST['submit']) ? strtolower($_POST['submit']) : '';

		if ($action === 'install_controller_code') {
			$this->_flash($this->_installAllBlocks());
		} elseif ($this->uri->segment(4) === 'delete' && $submit === 'submit') {
			$this->_flash($this->_deleteQuestion());
		}

		$data = array();
		$data['title'] = 'Anti Spam Questions';
		$data['controller_state'] = $this->_combinedControllerState();
		$data['images'] = $this->_iconImages();

		$this->db->from('settings');
		$this->db->where('setting_key', 'question');
		$data['models'] = $this->db->get()->result();

		$this->_regions['title'] .= 'Anti Spam Questions';
		$this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);
		$this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
			->view('index', $this->skin, 'admin', $data);

		Template::assign($this->_regions);
		Template::render();
	}

	public function create()
	{
		Auth::check_access('site/settings');

		$data = array();
		$data['title'] = 'Create Question';

		if (isset($_POST['submit']) && $_POST['submit'] === 'Submit') {
			$json = array(
				'question' => isset($_POST['question']) ? $_POST['question'] : '',
				'answer'   => $this->_cleanAnswers(isset($_POST['answer']) ? $_POST['answer'] : array()),
			);

			$this->ci->settings->add_new_setting(array(
				'setting_key'   => 'question',
				'setting_label' => 'Questions and Answer',
				'setting_value' => json_encode($json),
			));

			$this->_flash(array('success', 'Question added.'));
		}

		$this->_regions['title'] .= 'Create Question';
		$this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);
		$this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_css('custom', 'admin', $data);
		$this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
			->view('create', $this->skin, 'admin', $data);

		Template::assign($this->_regions);
		Template::render();
	}

	public function edit()
	{
		Auth::check_access('site/settings');

		$id = $this->uri->segment(5);

		if (isset($_POST['submit']) && $_POST['submit'] === 'Submit') {
			$json = array(
				'question' => isset($_POST['question']) ? $_POST['question'] : '',
				'answer'   => $this->_cleanAnswers(isset($_POST['answer']) ? $_POST['answer'] : array()),
			);

			$this->ci->settings->update_setting($id, array(
				'setting_value' => json_encode($json),
			), 'setting_id');

			$this->_flash(array('success', 'Question updated.'));
		}

		$data = array();
		$data['title'] = 'Edit Question';
		$query = $this->db->get_where('settings', array('setting_id' => $id));
		$data['model'] = ($query->num_rows() > 0) ? $query->row() : false;

		$this->_regions['title'] .= 'Edit Question';
		$this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_js('custom', 'admin', $data);
		$this->_regions['javascript'] .= $this->extension['nova_ext_anti_spam_questions']->inline_css('custom', 'admin', $data);
		$this->_regions['content'] = $this->extension['nova_ext_anti_spam_questions']
			->view('update', $this->skin, 'admin', $data);

		Template::assign($this->_regions);
		Template::render();
	}

	// ---------- helpers ----------

	private function _flash($result)
	{
		$flash = array(
			'status'  => ($result[0] === 'error') ? 'error' : 'success',
			'message' => text_output($result[1]),
		);
		$this->_regions['flash_message'] = Location::view('flash', $this->skin, 'admin', $flash);
	}

	private function _cleanAnswers($raw)
	{
		if ( ! is_array($raw)) {
			return array();
		}
		$out = array();
		foreach ($raw as $a) {
			$a = is_string($a) ? trim($a) : '';
			if ($a !== '') {
				$out[] = $a;
			}
		}
		return $out;
	}

	private function _deleteQuestion()
	{
		$id = $this->input->post('id', true);
		$id = is_numeric($id) ? (int) $id : false;
		if ($id === false) {
			return array('error', 'Invalid question id.');
		}
		$this->ci->settings->delete_setting($id);
		return array('success', 'Question deleted.');
	}

	private function _iconImages()
	{
		return array(
			'add' => array(
				'src'   => Location::img('icon-add.png', $this->skin, 'admin'),
				'alt'   => ucfirst(lang('actions_add')),
				'title' => ucfirst(lang('actions_add')),
				'class' => 'image inline_img_left',
			),
			'delete' => array(
				'src'   => Location::img('icon-delete.png', $this->skin, 'admin'),
				'alt'   => lang('actions_delete'),
				'title' => ucfirst(lang('actions_delete')),
				'class' => 'image',
			),
			'edit' => array(
				'src'   => Location::img('icon-edit.png', $this->skin, 'admin'),
				'alt'   => lang('actions_edit'),
				'title' => ucfirst(lang('actions_edit')),
				'class' => 'image',
			),
		);
	}

	// ---------- controller-block writer (contact + join in Main.php) ----------

	private function _blockMap()
	{
		$file = APPPATH.'controllers/Main.php';
		$txt  = dirname(__FILE__).'/../main.txt';

		return array(
			'contact' => array(
				'file'   => $file,
				'txt'    => $txt,
				'tag'    => 'contact',
				'method' => 'contact',
				'label'  => 'Contact form code',
			),
			'join' => array(
				'file'   => $file,
				'txt'    => $txt,
				'tag'    => 'join',
				'method' => 'join',
				'label'  => 'Join form code',
			),
		);
	}

	private function _combinedControllerState()
	{
		$states = array();
		foreach (array_keys($this->_blockMap()) as $which) {
			$states[] = $this->_controllerBlockState($which);
		}
		// Worst-of: missing_file > legacy > outdated > missing > current
		$priority = array('missing_file' => 4, 'legacy' => 3, 'outdated' => 2, 'missing' => 1, 'current' => 0);
		$worst = 'current';
		foreach ($states as $s) {
			if (($priority[$s] ?? -1) > $priority[$worst]) {
				$worst = $s;
			}
		}
		return $worst;
	}

	private function _installAllBlocks()
	{
		$ok = 0;
		$noop = 0;
		$errors = array();
		foreach (array_keys($this->_blockMap()) as $which) {
			$result = $this->_writeControllerBlock($which);
			if ($result[0] === 'error') {
				$errors[] = $result[1];
			} elseif (strpos($result[1], 'already up to date') !== false) {
				$noop++;
			} else {
				$ok++;
			}
		}

		if ( ! empty($errors)) {
			return array('error', implode(' ', $errors));
		}
		if ($ok === 0) {
			return array('success', 'Controller code is already up to date.');
		}
		return array('success', 'Controller code updated successfully.');
	}

	private function _controllerBlockState($which)
	{
		$map = $this->_blockMap();
		if ( ! isset($map[$which])) {
			return 'unknown';
		}
		$m = $map[$which];

		if ( ! file_exists($m['file'])) {
			return 'missing_file';
		}

		$file = file_get_contents($m['file']);
		$txt  = file_exists($m['txt']) ? file_get_contents($m['txt']) : '';

		$installedVersion = $this->_blockVersion($file, $m['tag']);
		$currentVersion   = $this->_blockVersion($txt,  $m['tag']);

		if ($installedVersion !== null) {
			return ($installedVersion === $currentVersion) ? 'current' : 'outdated';
		}

		if (preg_match('/function\s+'.preg_quote($m['method'], '/').'\s*\(/', $file)) {
			return 'legacy';
		}

		return 'missing';
	}

	private function _blockVersion($content, $tag)
	{
		if (preg_match('/nova_ext_anti_spam_questions:'.preg_quote($tag, '/').' v(\d+) START/', $content, $match)) {
			return (int) $match[1];
		}
		return null;
	}

	private function _readBlockFromTxt($txtPath, $tag)
	{
		if ( ! file_exists($txtPath)) {
			return null;
		}
		$content = file_get_contents($txtPath);
		$pattern = '/[ \t]*\/\*\s*nova_ext_anti_spam_questions:'.preg_quote($tag, '/')
			.' v\d+ START.*?nova_ext_anti_spam_questions:'.preg_quote($tag, '/').' END\s*\*\//s';
		if (preg_match($pattern, $content, $match)) {
			return rtrim($match[0], "\r\n");
		}
		return null;
	}

	private function _writeControllerBlock($which)
	{
		$map = $this->_blockMap();
		if ( ! isset($map[$which])) {
			return array('error', 'Unknown block.');
		}
		$m = $map[$which];

		$state = $this->_controllerBlockState($which);

		if ($state === 'current') {
			return array('success', $m['label'].' is already up to date.');
		}
		if ($state === 'missing_file') {
			return array('error', 'Could not find '.$m['file'].'.');
		}

		$block = $this->_readBlockFromTxt($m['txt'], $m['tag']);
		if ($block === null) {
			return array('error', 'Cannot find the '.$m['tag'].' block in '.basename($m['txt']).'.');
		}

		$file = file_get_contents($m['file']);

		if ($state === 'outdated') {
			$pattern = '/[ \t]*\/\*\s*nova_ext_anti_spam_questions:'.preg_quote($m['tag'], '/')
				.' v\d+ START.*?nova_ext_anti_spam_questions:'.preg_quote($m['tag'], '/').' END\s*\*\//s';
			$new = preg_replace($pattern, $block, $file, 1, $count);
			if ($count !== 1) {
				return array('error', 'Could not locate the managed '.$m['tag'].' block in '.basename($m['file']).'.');
			}
			$file = $new;
		} elseif ($state === 'legacy') {
			$span = $this->_findUnmarkedMethodSpan($file, $m['method']);
			if ($span === null) {
				return array('error', 'Could not parse the existing '.$m['method'].'() method in '.basename($m['file']).'.');
			}
			$file = substr($file, 0, $span[0]).$block."\n".substr($file, $span[1]);
		} else {
			$pos = strrpos($file, '}');
			if ($pos === false) {
				return array('error', basename($m['file']).' is not in the expected format.');
			}
			$file = rtrim(substr($file, 0, $pos))."\n\n".$block."\n}\n";
		}

		file_put_contents($m['file'], $file);

		return array('success', $m['label'].' updated successfully.');
	}

	/**
	 * Locate the byte span of an unmarked $methodName declaration in $content.
	 * Returns array($start, $end) (end exclusive, includes the trailing newline
	 * if present), or null if the method can't be cleanly located. A minimal
	 * lexer is used so braces, comments, and string literals don't fool the
	 * counter.
	 */
	private function _findUnmarkedMethodSpan($content, $methodName)
	{
		$len = strlen($content);
		$state = 'normal';
		$functionPositions = array();
		$i = 0;

		while ($i < $len) {
			$c = $content[$i];
			$next = ($i + 1 < $len) ? $content[$i + 1] : '';

			if ($state === 'normal') {
				if ($c === "'") { $state = 'single'; $i++; continue; }
				if ($c === '"') { $state = 'double'; $i++; continue; }
				if ($c === '/' && $next === '/') { $state = 'line_comment'; $i += 2; continue; }
				if ($c === '/' && $next === '*') { $state = 'block_comment'; $i += 2; continue; }
				if ($c === 'f'
					&& substr($content, $i, 8) === 'function'
					&& ($i === 0 || ! self::_isIdentChar($content[$i - 1]))
					&& ($i + 8 >= $len || ! self::_isIdentChar($content[$i + 8]))) {
					$functionPositions[] = $i;
					$i += 8;
					continue;
				}
			} elseif ($state === 'single') {
				if ($c === '\\') { $i += 2; continue; }
				if ($c === "'") $state = 'normal';
			} elseif ($state === 'double') {
				if ($c === '\\') { $i += 2; continue; }
				if ($c === '"') $state = 'normal';
			} elseif ($state === 'line_comment') {
				if ($c === "\n") $state = 'normal';
			} elseif ($state === 'block_comment') {
				if ($c === '*' && $next === '/') { $state = 'normal'; $i += 2; continue; }
			}
			$i++;
		}

		foreach ($functionPositions as $fnPos) {
			$p = $fnPos + 8;
			while ($p < $len && ctype_space($content[$p])) {
				$p++;
			}
			$nameLen = strlen($methodName);
			if ($p + $nameLen > $len) continue;
			if (substr($content, $p, $nameLen) !== $methodName) continue;
			if ($p + $nameLen < $len && self::_isIdentChar($content[$p + $nameLen])) continue;

			$k = $fnPos - 1;
			while ($k >= 0 && ($content[$k] === ' ' || $content[$k] === "\t")) {
				$k--;
			}
			foreach (array('static', 'final', 'abstract', 'protected', 'public', 'private') as $kw) {
				$klen = strlen($kw);
				if ($k - $klen + 1 >= 0
					&& substr($content, $k - $klen + 1, $klen) === $kw
					&& ($k - $klen < 0 || ! self::_isIdentChar($content[$k - $klen]))) {
					$k -= $klen;
					while ($k >= 0 && ($content[$k] === ' ' || $content[$k] === "\t")) {
						$k--;
					}
				}
			}
			$start = $k + 1;

			$q = $p + $nameLen;
			$bs = 'normal';
			$depth = 0;
			$started = false;
			while ($q < $len) {
				$c = $content[$q];
				$next = ($q + 1 < $len) ? $content[$q + 1] : '';
				if ($bs === 'normal') {
					if ($c === '{') {
						$depth++;
						$started = true;
					} elseif ($c === '}') {
						$depth--;
						if ($started && $depth === 0) {
							$end = $q + 1;
							if ($end < $len && $content[$end] === "\n") $end++;
							return array($start, $end);
						}
					} elseif ($c === "'") { $bs = 'single'; $q++; continue; }
					elseif ($c === '"') { $bs = 'double'; $q++; continue; }
					elseif ($c === '/' && $next === '/') { $bs = 'line_comment'; $q += 2; continue; }
					elseif ($c === '/' && $next === '*') { $bs = 'block_comment'; $q += 2; continue; }
				} elseif ($bs === 'single') {
					if ($c === '\\') { $q += 2; continue; }
					if ($c === "'") $bs = 'normal';
				} elseif ($bs === 'double') {
					if ($c === '\\') { $q += 2; continue; }
					if ($c === '"') $bs = 'normal';
				} elseif ($bs === 'line_comment') {
					if ($c === "\n") $bs = 'normal';
				} elseif ($bs === 'block_comment') {
					if ($c === '*' && $next === '/') { $bs = 'normal'; $q += 2; continue; }
				}
				$q++;
			}
			return null;
		}

		return null;
	}

	private static function _isIdentChar($ch)
	{
		return ctype_alnum($ch) || $ch === '_';
	}
}
