<?php
/**
 * @author Donii Sergii <s.doniy@infomir.com>.
 */

namespace sonrac\Swagger;

use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SwaggerCommand.
 *
 * @author Donii Sergii <s.donii@infomir.com>
 */
class SwaggerCommand extends Command
{
    /**
     * Application instance.
     *
     * @var \Silex\Application
     *
     * @author Donii Sergii <s.donii@infomir.com>
     */
    protected $app;

    /**
     * SwaggerCommand constructor.
     *
     * @param null                    $name
     * @param \Silex\Application|null $app
     *
     * @author Donii Sergii <s.donii@infomir.com>
     */
    public function __construct($name = null, Application $app = null)
    {
        parent::__construct($name);
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swagger:generate')
            ->addOption('source', 's', InputArgument::OPTIONAL, 'Path to source')
            ->addOption('output', 'o', InputArgument::OPTIONAL, 'Path to swagger.json folder for save')
            ->addOption('stdout', 'c', InputArgument::OPTIONAL, 'Print json to console', false)
            ->addOption('api-version', 'ver', InputArgument::OPTIONAL, 'API generated version')
            ->addOption('bootstrap', 'b', InputArgument::OPTIONAL, 'Bootstrap a php file for defining constants, etc.')
            ->addOption('debug', 'd', InputArgument::OPTIONAL, 'Enable debug mode', false)
            ->addOption('exclude', 'e', InputArgument::IS_ARRAY, 'Exclude paths or files', [])
            ->addOption('vendor-path', 'vp', InputArgument::OPTIONAL, 'Path to vendor dir', [])
            ->addOption('processor', 'p', InputArgument::IS_ARRAY, 'Additional swagger processors list', [])
            ->addArgument('path_to_project', InputArgument::OPTIONAL, 'Path to project');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getOption('api-version');
        $src = $input->getOption('source') ?: $input->getArgument('path_to_project');
        $out = $this->getOutputPath($input);
        $stdout = $input->getOption('stdout');
        $debug = $input->getOption('debug');
        $exclude = implode(',', $input->getOption('exclude') ?: []);
        $processor = implode(',', $input->getOption('processor') ?: []);
        $vendorPath = $this->getVendorDir($input);
        $bootstrap = $this->getBootstrapFile($input);

        $swaggerBin = rtrim($vendorPath, '/').'/bin/'.'swagger';

        $command = "{$swaggerBin} ";

        foreach ([
            'version' => $version,
            'stdout' => $stdout,
            'exclude' => $exclude,
            'processor' => $processor,
            'debug' => $debug,
            'output' => $out,
            'bootstrap' => $bootstrap,
                 ] as $name => $value) {
            if ($value) {
                if (is_bool($value)) {
                    $command .= " --{$name} ";
                } else {
                    $command .= " --{$name} {$value} {$src}";
                }
            }
        }

        echo PHP_EOL.PHP_EOL."Run command: {$command}".PHP_EOL;

        system($command);
    }

    /**
     * Get vendor dir path.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     *
     * @author Donii Sergii <s.donii@infomir.com>
     */
    private function getVendorDir(InputInterface $input)
    {
        $vendorPath = $input->getOption('vendor-path') ?: null;

        if (!$vendorPath && $this->app->offsetExists('swagger.vendor_path')) {
            $vendorPath = $this->app['swagger.vendor_path'];
        }

        if (!is_dir($vendorPath)) {
            throw new \InvalidArgumentException('Vendor path does not defined in $app[\'swagger.vendor_path\']');
        }

        return $vendorPath;
    }

    /**
     * Get bootstrap file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     *
     * @author Donii Sergii <s.donii@infomir.com>
     */
    private function getBootstrapFile(InputInterface $input)
    {
        $bootstrap = $input->getOption('bootstrap') ?: null;

        if (!$bootstrap && $this->app->offsetExists('swagger.bootstrap')) {
            $bootstrap = $this->app['swagger.bootstrap'];
        }

        if ($bootstrap && !is_file($bootstrap)) {
            throw new \InvalidArgumentException('Bootstrap file does not exists in $app[\'swagger.bootstrap\']');
        }

        return $bootstrap;
    }

    /**
     * Get bootstrap file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     *
     * @author Donii Sergii <s.donii@infomir.com>
     */
    private function getOutputPath(InputInterface $input)
    {
        $out = $input->getOption('output');

        if (!$out && $this->app->offsetExists('swagger.output')) {
            $out = $this->app['swagger.output'];
        }

        if ($out && (!is_writable(dirname($out)) || is_dir($out))) {
            throw new \InvalidArgumentException('Invalid output file path. Set swagger.output in application config');
        }

        return $out;
    }
}
