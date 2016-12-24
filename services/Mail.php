<?php

use Nette\Mail\Message;

date_default_timezone_set('PRC');

/**
 * Class Mail
 */

class Mail

{

    public $config;

    // [String] e-mail

    protected $from;

    // [Array] e-mail list

    protected $to;

    protected $title;

    protected $body;

    protected $mail;

    /**
     * Mail constructor.
     * @param $to
     */
    function __construct($values)

    {
        $this->mail = new Message;

        $this->config = require_once BASE_PATH . '/config/mail.php';

        $this->mail->setFrom($this->config['username']);

        if ( !is_array($values) ) {

            $values = [$values];

        }

        foreach ($values as $email) {

            $this->mail->addTo($email);

        }

    }

    /**
     * 发件人
     * @param null $from
     * @return $this
     */
    public function from($from=null)

    {

        if ( !$from ) {

            throw new InvalidArgumentException("邮件发送地址不能为空！");

        }

        $this->mail->setFrom($from);

        return $this;

    }

    /**
     * 收件人
     * @param null $to
     * @return Mail
     */
    public static function to($values=null)

    {

        if ( !$values ) {

            throw new InvalidArgumentException("邮件接收地址不能为空！");

        }

        return new Mail($values);

    }

    /**
     * 邮件标题
     * @param null $title
     * @return $this
     */
    public function title($title=null)

    {

        if ( !$title ) {

            throw new InvalidArgumentException("邮件标题不能为空！");

        }

        $this->mail->setSubject($title);

        return $this;

    }

    /**
     * 邮件内容
     * @param null $content
     * @return $this
     */
    public function content($content=null)

    {

        if ( !$content ) {

            throw new InvalidArgumentException("邮件内容不能为空！");

        }

        $this->mail->setHTMLBody($content);

        return $this;

    }

    function __destruct()
    {

        $mailer = new Nette\Mail\SmtpMailer($this->config);

        $mailer->send($this->mail);

    }

}