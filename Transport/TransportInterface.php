<?php

namespace Nibynool\SmppClientBundle\Transport;

/**
 * Interface TransportInterface
 */
interface TransportInterface
{
    /**
     * @return null
     */
    public function open();

    /**
     * @return null
     */
    public function close();

    /**
     * @param integer $length
     *
     * @return null|string
     */
    public function read($length);

    /**
     * @param integer $length
     *
     * @return null|string
     */
    public function readAll($length);

    /**
     * @param mixed   $buffer
     * @param integer $length
     */
    public function write($buffer, $length);
} 