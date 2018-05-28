<?php
/**
 * Provides Assertions
 **/
class Assert
{
    public static function AreEqual( $a, $b )
    {
    	if ( $a != $b )
    	{
    		throw new Exception( 'Subjects are not equal.' );
    	}
    }
    public static function IsANumber($a)
    {
        if (!is_numeric($a))
        {
            throw new Exception( 'Subject is not a number' );
        }
    }
}

/**
 * Provides a loggable entity with information on a test and how it executed
 **/
class TestResult
{
    protected $_testableInstance = null;

    protected $_isSuccess = false;
    public function getSuccess()
    {
    	return $this->_isSuccess;
    }

    protected $_output = '';
    public function getOutput()
    {
    	return $_output;
    }
    public function setOutput( $value )
    {
    	$_output = $value;
    }

    protected $_test = null;
    public function getTest()
    {
    	return $this->_test;
    }

    public function getName()
    {
    	return $this->_test->getName();
    }
    public function getComment()
    {
    	return $this->ParseComment( $this->_test->getDocComment() );
    }

    private function ParseComment( $comment )
    {
    	$lines = explode( "\n", $comment );
    	for( $i = 0; $i < count( $lines ); $i ++ )
    	{
    		$lines[$i] = trim( $lines[ $i ] );
    	}
    	return implode( "\n", $lines );
    }

    protected $_exception = null;
    public function getException()
    {
    	return $this->_exception;
    }

    static public function CreateFailure( Testable $object, ReflectionMethod $test, Exception $exception )
    {
    	$result = new self();
    	$result->_isSuccess = false;
    	$result->testableInstance = $object;
    	$result->_test = $test;
    	$result->_exception = $exception;

    	return $result;
    }
    static public function CreateSuccess( Testable $object, ReflectionMethod $test )
    {
    	$result = new self();
    	$result->_isSuccess = true;
    	$result->testableInstance = $object;
    	$result->_test = $test;

    	return $result;
    }
}

/**
 * Provides a base class to derive tests from
 **/
abstract class Testable
{
    protected $test_log = array();


    public function __construct()
    {
    }

    /**
     * Logs the result of a test. keeps track of results for later inspection, Overridable to log elsewhere.
     **/
    protected function Log( TestResult $result )
    {
    	$this->test_log[] = $result;
        printf("<div class='TestResult'><div class='%s'><div class='title'>%s</div></div>%s</div>" 
            ,$result->getSuccess() ? 'testSuccess' : 'testFailure'
            ,$result->getName()
            ,$result->getSuccess() ? '' : sprintf("<div class='testBody'>%s (lines:%d-%d; file:%s)</div><div class='exception'>%s</div>"
                ,$result->getComment()
                ,$result->getTest()->getStartLine()
                ,$result->getTest()->getEndLine()
                ,$result->getTest()->getFileName()
                ,$result->getException()
                )
        );

    }
    final public function RunTests()
    {
    	$class = new ReflectionClass( $this );
    	foreach( $class->GetMethods() as $method )
    	{
    		$methodname = $method->getName();
    		if ( strlen( $methodname ) > 4 && substr( $methodname, 0, 4 ) == 'Test' )
    		{
    			ob_start();
    			try
    			{
    				$this->$methodname();
    				$result = TestResult::CreateSuccess( $this, $method );
    			}
    			catch( Exception $ex )
    			{
    				$result = TestResult::CreateFailure( $this, $method, $ex );
    			}
    			$output = ob_get_clean();
    			$result->setOutput( $output );
    			$this->Log( $result );
    		}
    	}
    }
}
?>