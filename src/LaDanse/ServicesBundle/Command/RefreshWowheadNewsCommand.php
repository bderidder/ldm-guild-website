<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\CommonBundle\Helper\CommandExecutionContext;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Curl\Curl;

/**
 * Class RefreshWowheadNewsCommand
 * @package LaDanse\ServicesBundle\Command
 */
class RefreshWowheadNewsCommand extends ContainerAwareCommand
{
    const WOWHEAD_RSS_URL = "http://www.wowhead.com/news&rss";

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:refreshWowheadNews')
            ->setDescription('Refresh RSS news from wowhead')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = new CommandExecutionContext(
            $input,
            $output
        );

        $xmlString = $this->getXMLFromUrl($context, RefreshWowheadNewsCommand::WOWHEAD_RSS_URL);

        $xml = simplexml_load_string($xmlString);

        if ($xml === false)
        {
            echo "Failed loading XML: ";

            foreach(libxml_get_errors() as $error)
            {
                $context->error($error->message);
            }

            return;
        }

        $itemXMLList = $xml->channel->item;

        $items = array();

        $count = 0;

        foreach($itemXMLList as $itemXML)
        {
            $pubTime = strtotime((string)$itemXML->pubDate);

            $item = (object) array(
                'title'   => (string) $itemXML->title,
                'link'    => (string) $itemXML->link,
                'pubDate' => date('D, d M, H:i', $pubTime)
            );

            $count++;

            if ($count <= 10)
            {
                $items[] = $item;
            }
        }

        $this->getContainer()->get('cache')->save('wowhead.rss', $items);
    }

    private function getXMLFromUrl(CommandExecutionContext $context, $url)
    {
        $curl = new Curl();

        curl_setopt($curl->curl, CURLOPT_TIMEOUT_MS, 10000);

        $curl->get($url);

        if ($curl->error)
        {
            $errorCode = $curl->error_code;

            $context->error("Error while downloading XML " . $errorCode);

            $curl->close();
        }
        else
        {
            $xmlResponse = $curl->response;
            $curl->close();

            return $xmlResponse;
        }
    }
}