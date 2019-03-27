<?php

namespace LC\ILP\NewsMan\DataObjects;

class Settings
{
	const DEFAULT_ACTIVE       = 'def_active';
	const SUPPORT_LANG         = 'sup_lang';
	const DEFAULT_LANG         = 'def_lang';
	const CONTENT_MIN_CHARS    = 'cont_min_chars';
	const CONTENT_MAX_CHARS    = 'cont_max_chars';
	const CONTENT_ALLOWED_TAGS = 'cont_allowed_tags';
	const TEASER_LENGTH        = 'teaser_length';
	const TEASER_ELLIPSE       = 'teaser_ellipse';

	/** @var \ilSetting */
	protected $settings;

	/**
	 * Settings constructor.
	 * @param \ilSetting $setting
	 */
	public function __construct(\ilSetting $setting)
	{
		$this->settings = $setting;
	}

	public function getDefaultActive(): bool
	{
		return $this->settings->get(self::DEFAULT_ACTIVE, false);
	}

	public function setDefaultActive(bool $default_active)
	{
		$this->settings->set(self::DEFAULT_ACTIVE, $default_active);
	}

	public function getSupportLang(): bool
	{
		return $this->settings->get(self::SUPPORT_LANG, false);
	}

	public function setSupportLang(bool $support_lang)
	{
		$this->settings->set(self::SUPPORT_LANG, $support_lang);
	}

	public function getDefaultLang(): int
	{
		return $this->settings->get(self::DEFAULT_LANG, 0);
	}

	public function setDefaultLang(int $default_lang)
	{
		$this->settings->set(self::DEFAULT_LANG, $default_lang);
	}

	public function getContentMinChars(): int
	{
		return $this->settings->get(self::CONTENT_MIN_CHARS, 1);
	}

	public function setContentMinChars(int $min_chars)
	{
		$this->settings->set(self::CONTENT_MIN_CHARS, $min_chars);
	}

	public function getContentMaxChars(): int
	{
		return $this->settings->get(self::CONTENT_MAX_CHARS, 9999);
	}

	public function setContentMaxChars(int $max_chars)
	{
		$this->settings->set(self::CONTENT_MAX_CHARS, $max_chars);
	}

	public function getContentAllowedTags(): array
	{
		return json_decode($this->settings->get(self::CONTENT_ALLOWED_TAGS, $this->getDefaultAllowedTags()), true);
	}

	public function setContentAllowedTags(array $allowed_tags)
	{
		$this->settings->set(self::CONTENT_ALLOWED_TAGS, json_encode($allowed_tags));
	}

	/**
	 * @return array
	 */
	public function getContentPossibleTags(): array
	{
		return [
			"font", "a", "blockquote", "br", "cite", "code", "dd", "div", "dl", "dt", "em",
			"h1", "h2", "h3", "h4", "h5", "h6", "hr", "img", "li", "object", "ol", "p",
			"param", "pre", "span", "strike", "strong", "sub", "sup", "table", "td", "tr",
			"u", "ul", "ruby", "rbc", "rtc", "rb", "rt", "rp"
		];
	}

	private function getDefaultAllowedTags(): string
	{
		return json_encode([
			"font" => "font", "a" => "a", "br" => "br",
			"div" => "div", "h1" => "h1", "h2" => "h2",
			"h3" => "h3", "h4" => "h4", "h5" => "h5",
			"h6" => "h6", "hr" => "hr", "li" => "li",
			"ol" => "ol", "p" => "p", "pre" => "pre",
			"span" => "span", "strong" => "strong", "sub" => "sub",
			"sup" => "sup", "u" => "u", "ul" => "ul",
		]);
	}

}