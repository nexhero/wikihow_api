<?php
namespace Howapi\Resources;
use PHPHtmlParser\Dom;

/**
 *
 */
class WikiHow
{
  protected $url;
  protected $dom;

  public function responseCode(){
    $ch = curl_init($this->url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,10);
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpcode;
  }
  public function validate(){
    $parse = parse_url($this->url);
    if ($parse['host']!='www.wikihow.com') {
      return $error = array('error' => 'incorrect host', );
    }else {
      if ($this->responseCode()!=200) {
        return $error = array('response code' => $this->responseCode(), );
      }
    }
    return true;
  }

  function __construct($url)
  {
    $this->url = $url;
    $dom = new Dom;
    $dom->load($url);
    $this->dom = $dom;
  }


    /**
     * Get the value of Url
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the Title of the page
     *
     * @return mixed
     */
    public function getTitle()
    {
      $title = $this->dom->getElementsByClass('firstHeading');
      return $title->firstChild()->text;
    }

    /**
     * Get the value of Steps
     *
     * @return mixed
     */
    public function getSections()
    {
      $data = array();
      $body= $this->dom->getElementById('bodycontents');
      $dom = new Dom;
      $dom->load($body);
      $sections = $dom->getElementsByClass('steps');
      $flag = 0;
      foreach ($sections as $key => $step) {
        $ol_var =  $dom->load($step)->getElementsByClass('steps_list_2');
        $li_var = $dom->load($ol_var)->getElementsByTag('li');
        foreach ($li_var as $key => $value) {
          $data[$flag]['stepNumber'] = $flag+1;
          $data[$flag]['header'] = $dom->load($value)->getElementsByClass('whb')->text;
          $data[$flag]['media'] = $dom->load($value)->getElementsByTag('img')->getAttribute('src');
          $data[$flag]['body'] = $dom->load($value)->getElementsByClass('step')->text;
          $flag++;
        }
        //echo $content;
      }
      return $data;
    }
    public function getDescription()
    {
      $intro = $this->dom->getElementById('intro');
      $dom = new Dom;
      $dom->load($intro);
      $p = $dom->getElementsByTag('p');
      if (count($p)>1) {
        return htmlentities($p[1]);
      }
      return  htmlentities($p);
    }
    public function getJson()
    {
      $data  = array();
      $validate = $this->validate();
      if (is_array($this->validate())) {
        $data = $validate;
      }else{
        $data = array(
          'url' =>$this->getUrl() ,
          'title' =>$this->getTitle(),
          'description'=>$this->getDescription(),
          'steps'=>$this->getSections(),
         );

      }
      return json_encode($data);

    }
}
