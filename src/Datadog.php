<?php
/**
 * Created by PhpStorm.
 * User: yuehlin
 * Date: 6/6/17
 * Time: 1:33 PM
 */

namespace Shapeways\Datadog;

use DataDog\Statsd\Statsd;

class Datadog
{
  const SHAPESTOR_PICTURE_TAG    = 'picture';
  const SHAPESTOR_AVATAR_TAG     = 'avatar';
  const SHAPESTOR_SHOP_TAG       = 'shop';
  const SHAPESTOR_BLOG_TAG       = 'blog';
  const SHAPESTOR_UPS_TAG        = 'ups';
  const SHAPESTOR_MODEL_TAG      = 'model';
  const SHAPESTOR_ORDER_TAG      = 'order';
  const SHAPESTOR_OTHER_TAG      = 'other';
  const SHAPESTOR_UNKNOWN_TAG    = 'unknown';

  private $statsdClient = null;
  private $emitMetrics = false;
  private $statsdTags = [];
  private $statsdNamespace = null;

  /**
   * Datadog constructor.
   * @param $config
   * @param $statsdNamespace
   * @param $appName
   */
  public function __construct($config, $statsdNamespace, $appName)
  {
    $this->emitMetrics = $config->emitMetrics ?? false;
    $this->statsdNamespace = $statsdNamespace;
    $this->statsdClient = new Statsd();
    $this->statsdTags['appname'] = $appName;
    $this->statsdTags['status'] = 200;
    $this->statsdTags['env'] = $config->env;
  }

  /**
   * @param array $extraTags
   * @return array
   */
  public function getStatsdTags($extraTags=array()): array
  {
    return array_merge($this->statsdTags, $extraTags);
  }

  /**
   * @param $name
   * @param $value
   */
  public function setStatsdTag($name, $value)
  {
    $this->statsdTags[$name] = strval($value);
  }

  /**
   * @return Statsd|null
   */
  public function getStatsdClient()
  {
    return $this->statsdClient;
  }

  /**
   * @return boolean
   */
  public function isEmitMetrics(): bool
  {
    return $this->emitMetrics;
  }

  /**
   * @return string
   */
  public function getStatsdNamespace(): string
  {
    return $this->statsdNamespace;
  }

  /**
   * @param $key
   * @param int $delta
   * @param int $sampleRate
   * @param array $extraTags
   */
  public function increment($key, $delta=1, $sampleRate=1, $extraTags=array())
  {
    $tags = $this->getStatsdTags($extraTags);
    if ($this->isEmitMetrics()){
      $this->getStatsdClient()->updateStats($this->getStatsdNamespace() . $key, $delta, $sampleRate, $tags);
    }
  }

  /**
   * @param $key
   * @param $value
   * @param int $sampleRate
   * @param array $extraTags
   */
  public function gauge($key, $value, $sampleRate=1, $extraTags=array())
  {
    $tags = $this->getStatsdTags($extraTags);
    if ($this->isEmitMetrics()){
      $this->getStatsdClient()->gauge($this->getStatsdNamespace() . $key, $value, $sampleRate, $tags);
    }
  }

  /**
   * @param $key
   * @param $value
   * @param int $sampleRate
   * @param array $extraTags
   */
  public function histogram($key, $value, $sampleRate=1, $extraTags=array())
  {
    $tags = $this->getStatsdTags($extraTags);
    if ($this->isEmitMetrics()){
      $this->getStatsdClient()->histogram($this->getStatsdNamespace() . $key, $value, $sampleRate, $tags);
    }
  }

  /**
   * @param $key
   * @param $time
   * @param int $sampleRate
   * @param array $extraTags
   */
  public function timing($key, $time, $sampleRate=1, $extraTags=array())
  {
    $tags = $this->getStatsdTags($extraTags);
    if ($this->isEmitMetrics()) {
      $this->getStatsdClient()->timing($this->getStatsdNamespace() . $key, intval($time), $sampleRate, $tags);
    }
  }

  /**
   * @param $key
   * @param $value
   * @param int $sampleRate
   * @param array $extraTags
   */
  public function set($key, $value, $sampleRate=1, $extraTags=array())
  {
    $tags = $this->getStatsdTags($extraTags);
    if ($this->isEmitMetrics()){
      $this->getStatsdClient()->set($this->getStatsdNamespace() . $key, $value, $sampleRate, $tags);
    }
  }
}
