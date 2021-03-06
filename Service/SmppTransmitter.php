<?php

namespace Nibynool\SmppClientBundle\Service;

use Nibynool\SmppClientBundle\Encoder\GsmEncoder;
use Nibynool\SmppClientBundle\SMPP;
use Nibynool\SmppClientBundle\SmppCore\SmppAddress;
use Nibynool\SmppClientBundle\SmppCore\SmppTag;
use Nibynool\SmppClientBundle\Transport\SocketTransport;
use Nibynool\SmppClientBundle\Transport\TransportInterface;
use Nibynool\SmppClientBundle\SmppCore\SmppClient;

/**
 * Class SmppWrapper
 */
class SmppTransmitter
{
    private $transportParamters;
    private $login;
    private $password;
    private $signature;
    private $debug;
    private $nullTerminate;

    /** @var TransportInterface */
    private $transport;
    /** @var SmppClient */
    private $smpp;

    /**
     * @param array  $transportParamters
     * @param string $login
     * @param string $password
     * @param string $signature
     * @param array  $debug
     */
    public function __construct(array $transportParamters, $login, $password, $signature, array $debug, $nullTerminate)
    {
        $this->transportParamters = $transportParamters;
        $this->login = $login;
        $this->password = $password;
        $this->signature = $signature;
        $this->debug = $debug;
        $this->nullTerminate = $nullTerminate;
    }

    /**
     * @param string $to
     * @param string $message
     * @param string $from
     * @param bool $returnStatus
     * @param null|SmppTag[] $tags
     * @param int $dataCoding
     *
     * @return string|array|void
     */
    public function send($to, $message, $from = null, $returnStatus = false, $tags = null, $dataCoding = SMPP::DATA_CODING_DEFAULT)
    {
        if ($dataCoding === SMPP::DATA_CODING_UCS2) {
            $message = iconv('utf-8', "UTF-16BE", $message);
        } else {
            $message = GsmEncoder::utf8_to_gsm0338($message);
        }
        
        if ($from === null)
        {
            $from = $this->signature;
        }

        if (is_numeric($from))
        {
            $from = new SmppAddress(intval($from), SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);
        }
        else
        {
            $from = new SmppAddress($from, SMPP::TON_ALPHANUMERIC);
        }
        $to = new SmppAddress(intval($to), SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);

        $this->openSmppConnection();
        if ($returnStatus)
        {
            $this->smpp->setReturnStatus(true);
        }
        $response = $this->smpp->sendSMS($from, $to, $message, $tags, $dataCoding);
        $this->closeSmppConnection();

        return $response;
    }

    public function setFinalDeliveryReceipt($type)
    {
        if (!$this->smpp) $this->openSmppConnection();
        $this->smpp->setFinalDeliveryReceipt($type);
    }

    public function setSMEDeliveryReceipt($type)
    {
        if (!$this->smpp) $this->openSmppConnection();
        $this->smpp->setSMEDeliveryReceipt($type);
    }

    public function setIntermediateDeliveryReceipt($type)
    {
        if (!$this->smpp) $this->openSmppConnection();
        $this->smpp->setIntermediateDeliveryReceipt($type);
    }

    private function openSmppConnection()
    {
        $this->transport = new SocketTransport($this->transportParamters[0], $this->transportParamters[1]);
        $this->transport->setSendTimeout($this->transportParamters[2]);

        $this->smpp = new SmppClient($this->transport);

        $this->smpp->smsNullTerminateOctetStrings = $this->nullTerminate;

        $this->transport->debug = $this->debug['transport'];
        $this->smpp->debug = $this->debug['smpp'];

        $this->transport->open();
        $this->smpp->bindTransmitter($this->login, $this->password);
    }

    private function closeSmppConnection()
    {
        $this->smpp->close();
        $this->transport->close();
    }

    public function getLastStatusCode()
    {
        return $this->smpp->getLastStatus();
    }
} 
