<?php

namespace common\widgets;

use Yii;
use yii\bootstrap5\Alert as BootstrapAlert;
use yii\bootstrap5\Widget;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the order they were set.
 */
class Alert extends Widget
{
    /**
     * {@inheritdoc}
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-success',
        'info'    => 'alert-info',
        'warning' => 'alert-warning',
    ];

    /**
     * {@inheritdoc}
     */
    public $closeButton = [];


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendClass = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        foreach ($flashes as $type => $flash) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }

            foreach ((array) $flash as $i => $message) {
                echo BootstrapAlert::widget([
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => array_merge($this->options, [
                        'id' => $this->getId() . '-' . $type . '-' . $i,
                        'class' => $this->alertTypes[$type] . $appendClass,
                    ]),
                ]);
            }

            $session->removeFlash($type);
        }
    }
}
