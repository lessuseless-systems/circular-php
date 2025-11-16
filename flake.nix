{
  description = "Circular Protocol PHP SDK";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = nixpkgs.legacyPackages.${system};

        php = pkgs.php83.buildEnv {
          extensions = ({ enabled, all }: enabled ++ (with all; [
            curl
            mbstring
            openssl
          ]));
        };

        composer = pkgs.php83Packages.composer;

      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            php
            composer
          ];

          shellHook = ''
            echo "🔵 Circular Protocol PHP SDK Development Environment"
            echo "PHP Version: $(php -v | head -1)"
            echo "Composer Version: $(composer -V)"
            echo ""
            echo "Available commands:"
            echo "  composer install    - Install dependencies"
            echo "  composer test       - Run all tests"
            echo "  composer test:unit  - Run unit tests"
            echo "  composer cs:check   - Check code style"
            echo "  composer phpstan    - Run static analysis"
            echo ""
          '';
        };

        apps = {
          test = {
            type = "app";
            program = toString (pkgs.writeShellScript "test" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              # Ensure dependencies are installed
              if [ ! -d vendor ]; then
                echo "📦 Installing dependencies..."
                composer install --no-interaction
              fi

              echo "🧪 Running PHPUnit tests..."
              vendor/bin/phpunit --no-coverage tests/
            '');
          };

          test-unit = {
            type = "app";
            program = toString (pkgs.writeShellScript "test-unit" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              if [ ! -d vendor ]; then
                composer install --no-interaction
              fi

              echo "🧪 Running Unit Tests..."
              vendor/bin/phpunit --no-coverage tests/CircularProtocolUnitTest.php
            '');
          };

          test-integration = {
            type = "app";
            program = toString (pkgs.writeShellScript "test-integration" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              if [ ! -d vendor ]; then
                composer install --no-interaction
              fi

              echo "🧪 Running Integration Tests..."
              echo "⚠️  Note: These tests require a mock server on localhost:8080"
              vendor/bin/phpunit --no-coverage tests/CircularProtocolIntegrationTest.php
            '');
          };

          cs-check = {
            type = "app";
            program = toString (pkgs.writeShellScript "cs-check" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              if [ ! -d vendor ]; then
                composer install --no-interaction
              fi

              echo "🔍 Checking code style (PSR-12)..."
              vendor/bin/phpcs src tests
            '');
          };

          phpstan = {
            type = "app";
            program = toString (pkgs.writeShellScript "phpstan" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              if [ ! -d vendor ]; then
                composer install --no-interaction
              fi

              echo "🔍 Running PHPStan static analysis..."
              vendor/bin/phpstan analyse src tests
            '');
          };

          install = {
            type = "app";
            program = toString (pkgs.writeShellScript "install" ''
              export PATH="${php}/bin:${composer}/bin:$PATH"

              echo "📦 Installing Composer dependencies..."
              composer install --no-interaction
              echo "✅ Dependencies installed!"
            '');
          };
        };

        packages.default = pkgs.stdenv.mkDerivation {
          pname = "circular-protocol-php";
          version = "1.0.9";

          src = ./.;

          buildInputs = [ php composer ];

          buildPhase = ''
            composer install --no-dev --no-interaction --optimize-autoloader
          '';

          installPhase = ''
            mkdir -p $out
            cp -r * $out/
          '';
        };
      }
    );
}
