<?php
  class myHTTPRequest {
    public $proto, $method, $uri, $host, $port;
    public $header, $postData, $getData, $cookie = Array();

    public function __construct($data, $proto) {
      $this->proto = $proto;
      $tmp = explode(" ",$data);
      $this->method = $tmp[0];
      $this->setUri($tmp[1]);
      $this->strToObj($data);
    }

    private function setUri($string) {
      $tmp = explode("?", $string);
      $this->uri = preg_replace("/(https?:\/\/[-a-zA-Z0-9@:%._\+~#=]{2,256})(\/.*)/", "$2", $tmp[0]);
      if (isset($tmp[1])) { $this->extractData($tmp[1],0); }
      return True;
    }

    public function strToObj($data) {
        $headVSparam = explode("\n\n", $data);
        if (isset($headVSparam[1])) { $this->extractData($headVSparam[1],1); }
        $header = explode("\n",$headVSparam[0]);
        array_shift($header);
        foreach ($header as $ele) {
          preg_match("/^([0-9a-zA-Z\-_]+):\s?(.*)$/", $ele, $tmp);
          switch (strtolower($tmp[1])) {
            case 'cookie':
              $this->extractCookie($tmp[2]);
              break;
            case 'host':
              $this->extractHost($tmp[2]);
              break;
            default:
              $this->header[$tmp[1]]=preg_replace('/^ /','',$tmp[2]);
              break;
          }
        }
    }

    public function extractData($string, $index) {
      foreach (explode("&",$string) as $ele) {
        $tmp=explode("=",$ele);
        if ($index === 0) {
          $this->getData[$tmp[0]]=$tmp[1];
        } else {
          $this->postData[$tmp[0]]=$tmp[1];
        }
      }
    }

    public function extractCookie($string) {
      foreach (explode(";",$string) as $ele) {
        $tmp=explode("=",$ele);
        $this->cookie[preg_replace('/^ /','',$tmp[0])]=$tmp[1];
      }
    }

    public function extractHost($string) {
      $array = explode(":",$string);
      $this->host=preg_replace('/^ /','',$array[0]);
      if (! isset($array[1])) {
        switch ($this->proto) {
          case 'http':
            $this->port=80;
            break;
          case 'https':
            $this->port=443;
            break;
        }
      } else {
        $this->port=$array[1];
      }
    }
  }

  class myCurl {
    function __construct($req) {
        $this->ch = curl_init();
        $this->setUrl($req);
        curl_setopt($this->ch,CURLOPT_PORT,$req->port);
        if (strtolower($req->method)==="post") {
          curl_setopt($this->ch,CURLOPT_POST,1);
          $this->setPostData($req);

        }
        $this->setCookie($req);
        $this->setCustomHeader($req);
    }
    public function exec() {
      curl_setopt($this->ch, CURLOPT_HEADER, true);
      curl_exec($this->ch);
    }
    private function setUrl($req) {
      $queryString="";
      if ($req->getData) {
        $queryString = "?";
        foreach ($req->getData as $key => $value) {
          $queryString = $queryString.$key."=".$value."&";
        }
        $queryString = substr($queryString,0,-1);
      }
      $tmp = "$req->proto://$req->host:$req->port$req->uri$queryString";
      curl_setopt($this->ch,CURLOPT_URL,$tmp);
    }
    private function setPostData($req) {
      $postData = "";
      foreach ($req->postData as $key => $value) {
        $postData = $postData.$key."=".$value."&";
      }
      $postData = substr($postData,0,-1);
      curl_setopt($this->ch,CURLOPT_POSTFIELDS,$postData);
    }
    private function setCookie($req) {
      $cookie = "";
      foreach ($req->cookie as $key => $value) {
        $cookie = "$cookie $key=$value;";
      }
      curl_setopt($this->ch,CURLOPT_COOKIE,$cookie);
    }
    private function setCustomHeader($req) {
      $header = Array();
      foreach ($req->header as $key => $value) {
        array_push($header,"$key: $value");
      }
      curl_setopt($this->ch,CURLOPT_HTTPHEADER,$header);
    }
  }

  if (isset($_POST['request'])) {
      if ($_POST['request'] != "") {
      $request = $_POST['request'];
      $req = new myHTTPRequest($request, $_POST['proto']);
      $curl = new myCurl($req);
      $curl->exec();
    }
  }

 ?>
