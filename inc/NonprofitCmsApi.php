<?
class NonprofitCmsApi
{

	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
	private $apiKey = '';
	private $apiUrl = '';
	private $baseUrl = '';
	public function NonprofitCmsApi($apiKey, $apiUrl)
	{
		$apiUrl = str_replace('https:', 'http:', $apiUrl);
		if (!$this->endsWith($apiUrl, '/'))
				$apiUrl .= '/';

        $this->baseUrl = $apiUrl;
		
		$apiUrl .= 'member/api/';
		
		$this->apiKey = $apiKey;
		$this->apiUrl = $apiUrl;
	}

    function getForgotPasswordUrl()
    {
        return $this->baseUrl;
    }

    function getCreateAccountUrl()
    {
        return $this->baseUrl;
    }

    function doSingleSignOn($username, $password, $url = '')
    {
        $data = array(
            'email' => $username,
            'password' => $password);

        $response = $this->SendRequest("users/validateAndCreateSsoToken", "POST", $data);

        if ($response->ssoToken)
        {
            return $this->baseUrl . '/member/Account/SingleSignOn?email=' . $username . '&token=' . $response->ssoToken . '&redirectUrl=' . urlencode($url);
        }
        else
            return false;

        //get SSO Token
        //Redirect
    }

	private function CreateSignature($content)
	{			
		return hash_hmac("sha1", $content, $this->apiKey);
	}
	
	private function SendRequest($path, $method, $content)
	{
		$content = json_encode( $content );
		$url = $this->apiUrl . $path;
		$sig = $this->CreateSignature($url . $content);
		
		$options = array(
			'http' => array(
				'method'  => $method,
				'content' => $content,
				'header'=>  "Content-Type: application/json\r\n" .
					"Accept: application/json\r\n" .
					"signature: " . $sig . "\r\n"
			)
		);

		$context  = stream_context_create( $options );
		$result = file_get_contents( str_replace('http:', 'https:', $url), false, $context );
		$response = json_decode( $result );
		
		return $response;
	}

	public function authenticate($username, $password)
	{
		$data = array(
			'email' => $username,
			'password' => $password);		
		
		@$response = $this->SendRequest("users/validate", "POST", $data);
        if ($response->isSuccessful)
        {
            return $this->GetInfo($username);
        }
        else
            return false;
	}

	public function GetInfo($email)
	{
		$response =  $this->SendRequest("users/" . $email, "GET", null);		
		$fields = $response->baseProfile->fields;

		$userRole =  $this->getMagicFieldValue($fields);
        return array($userRole);
	}
	
	
	private function getMagicField($fields)
	{
		foreach($fields as $f)
		{
			if ($f->id == '50e1818c-33b3-4f51-8483-c705c0ea9b56')
				return $f->listValues;
		}
	}
	
	private function getMagicFieldValue($fields)
	{
		foreach($fields as $f)
		{
			if ($f->id == '50e1818c-33b3-4f51-8483-c705c0ea9b56')
				return $f->selectedValueId;
		}
	}
	
	public function getRoles()
	{
		$response = $this->SendRequest("baseProfileForm/", "GET", null);
		$fields = $response->fields;
		$roles = $this->getMagicField($fields);

        $roleDictionary = array();

        foreach($roles as  $v)
        {
            if ($v->id == '00000000-0000-0000-0000-000000000000')
                $roleDictionary[$v->id] = 'Authenticated User';

            else
                $roleDictionary[$v->id] = $v->value;
        }

        return $roleDictionary;
    }
}
?>