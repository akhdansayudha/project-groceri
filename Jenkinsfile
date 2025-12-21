pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
        IMAGE_NAME = 'vektora'
        AZURE_WEBAPP_NAME = 'project-vektora'
        AZURE_RESOURCE_GROUP = 'project-vektora-group'
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
                    // PERBAIKI: Gunakan bat, bukan sh
                    bat "docker build -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:%BUILD_ID% -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest ."
                }
            }
        }

        stage('Push to Registry') {
            steps {
                script {
                    withCredentials([usernamePassword(
                        credentialsId: 'docker-hub-credential',
                        usernameVariable: 'DOCKER_USERNAME',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )]) {
                        // Login ke Docker Hub
                        bat "echo ${DOCKER_PASSWORD} | docker login --username ${DOCKER_USERNAME} --password-stdin"
                        
                        // Push image
                        bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:%BUILD_ID%"
                        bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
                        
                        echo "✅ Image berhasil di-push: ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:%BUILD_ID%"
                    }
                }
            }
        }
        stage('Test Azure CLI Access') {
            steps {
                script {
                    echo '=== Testing Azure CLI from Jenkins ==='
                    
                    // Test 1: Coba dengan full path
                    bat '''
                    echo "Test 1: Using full path..."
                    "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az" --version
                    '''
                    
                    // Test 2: Coba dengan PATH biasa
                    bat '''
                    echo "Test 2: Using PATH..."
                    az --version || echo "az not found in PATH"
                    '''
                    
                    // Test 3: Cek environment
                    bat '''
                    echo "Test 3: Checking environment..."
                    echo User: %USERNAME%
                    echo Path: %PATH%
                    '''
                }
            }
        }
        stage('Deploy to Azure') {
            steps {
                script {
                    echo '🚀 Starting deployment to Azure Web App...'
                    
                    withCredentials([usernamePassword(
                        credentialsId: 'azure-webapp-publish',
                        usernameVariable: 'AZURE_USERNAME',
                        passwordVariable: 'AZURE_PASSWORD'
                    )]) {
                        // GUNAKAN FULL PATH ke az.exe
                        bat """
                        "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az" login --service-principal -u %AZURE_USERNAME% -p %AZURE_PASSWORD% --tenant common
                        
                        "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az" webapp config container set ^
                          --name %AZURE_WEBAPP_NAME% ^
                          --resource-group %AZURE_RESOURCE_GROUP% ^
                          --docker-custom-image-name %DOCKER_REGISTRY%/%IMAGE_NAME%:latest ^
                          --docker-registry-server-url https://index.docker.io
                        
                        "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az" webapp restart ^
                          --name %AZURE_WEBAPP_NAME% ^
                          --resource-group %AZURE_RESOURCE_GROUP%
                        """
                        
                        echo '⏳ Waiting for deployment to complete...'
                        sleep(time: 30, unit: 'SECONDS')
                    }
                }
            }
        }

        stage('Health Check') {
            steps {
                script {
                    echo '🏥 Checking application health...'
                    // Gunakan bat untuk curl di Windows
                    bat "curl -f --retry 3 --retry-delay 10 %APP_URL% || echo 'App might still be starting...'"
                    
                    echo '========================================'
                    echo '✅ DEPLOYMENT SUCCESSFUL!'
                    echo "🌐 Your app is live at: %APP_URL%"
                    echo "📦 Docker Image: %DOCKER_REGISTRY%/%IMAGE_NAME%:%BUILD_ID%"
                    echo '========================================'
                }
            }
        }
    }
    
    post {
        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}
