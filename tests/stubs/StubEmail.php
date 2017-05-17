<?php

class StubEmail extends Email implements TestOnly
{
    /**
     * Overrides email sending
     *
     * @param string $messageID Optional message ID so the message can be identified in bounces etc.
     *
     * @return array
     */
    public function send($messageID = null)
    {
        return array(
            'to'      => $this->to,
            'subject' => $this->subject,
            'from'    => $this->from,
        );
    }
}
