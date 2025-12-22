pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
        IMAGE_NAME = 'vektora'
        APP_URL = 'https://vektora-ffhggreufqf7dteg.southeastasia-01.azurewebsites.net'
    }

    stages {

        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    bat """
                    docker build ^
                      -t %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID% ^
                      -t %DOCKER_REGISTRY%/%IMAGE_NAME%:latest .
                    """
                }
            }
        }

        stage('Push to Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'docker-hub-credential',
                    usernameVariable: 'DOCKER_USERNAME',
                    passwordVariable: 'DOCKER_PASSWORD'
                )]) {
                    bat "echo %DOCKER_PASSWORD% | docker login --username %DOCKER_USERNAME% --password-stdin"
                    bat "docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID%"
                    bat "docker push %DOCKER_REGISTRY%/%IMAGE_NAME%:latest"
                }
            }
        }

        stage('Deploy Triggered') {
            steps {
                echo '''
                ========================================
                🚀 DEPLOYMENT TRIGGERED

                Image pushed to Docker Hub:
                - %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID%
                - %DOCKER_REGISTRY%/%IMAGE_NAME%:latest

                Azure App Service will:
                1. Pull latest image
                2. Restart container
                3. Warm up application

                ⏱ Expected time: 2–5 minutes
                ========================================
                '''
            }
        }
    }

    post {
        success {
            echo '✅ CI/CD pipeline SUCCESS. Deployment is now handled by Azure.'
            echo "🌐 App URL: ${APP_URL}"
        }

        failure {
            echo '❌ CI/CD pipeline FAILED. Check Jenkins logs.'
        }

        always {
            cleanWs()
            echo 'Pipeline finished.'
        }
    }
}
