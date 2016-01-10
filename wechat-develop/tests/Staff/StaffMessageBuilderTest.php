<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Message\Text;
use EasyWeChat\Staff\MessageBuilder;

class StaffMessageBuilderTest extends PHPUnit_Framework_TestCase
{
    public function getMessageBuilder()
    {
        $staff = Mockery::mock('EasyWeChat\Staff\Staff');
        $staff->shouldReceive('send')->andReturnUsing(function ($message) {
            return $message;
        });

        return new MessageBuilder($staff);
    }

    /**
     * Test message().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testMessage()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->message('hello');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertInstanceOf(Text::class, $MessageBuilder->message);

        // exception
        $MessageBuilder->message(new stdClass());
    }

    /**
     * Test by().
     */
    public function testBy()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->by('hello');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertEquals('hello', $MessageBuilder->account);
        $this->assertNull($MessageBuilder->by);
    }

    /**
     * Test to().
     */
    public function testTo()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->to('overtrue');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertEquals('overtrue', $MessageBuilder->to);
    }

    /**
     * Test send().
     *
     * @expectedException EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function testSend()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->message('hello')->by('overtrue')->to('easywechat')->send();

        $this->assertEquals('text', $response['msgtype']);
        $this->assertEquals('hello', $response['text']['content']);
        $this->assertEquals('overtrue', $response['customservice']['kf_account']);
        $this->assertEquals('easywechat', $response['touser']);

        // exception
        $MessageBuilder = $this->getMessageBuilder();
        $MessageBuilder->by('overtrue')->to('easywechat')->send();
    }
}
