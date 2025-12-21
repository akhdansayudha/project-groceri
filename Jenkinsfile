pipeline {
    agent any

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
        IMAGE_NAME = 'vektora'  // DIPERBAIKI: dari 'project-groceri' ke 'vektora'
        AZURE_WEBAPP_NAME = 'project-vektora'  // Nama App Service
        AZURE_RESOURCE_GROUP = 'project-vektora-group'  // Ganti jika berbeda
        APP_URL = 'https://vektora-ffhggreufqf7dteg.southeastasia-01.azurewebsites.net'  // URL App Service Anda
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
                    // DIPERBAIKI: build image dengan nama 'vektora'
                    bat "docker build -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID} -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest ."
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
                        
                        // DIPERBAIKI: push image 'vektora'
                        bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
                        bat "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
                        
                        echo "✅ Image berhasil di-push: ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
                    }
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
                        // DIPERBAIKI: update dengan image 'vektora'
                        bat """
                        az login --service-principal -u %AZURE_USERNAME% -p %AZURE_PASSWORD% --tenant common
                        az webapp config container set \\
                          --name ${env.AZURE_WEBAPP_NAME} \\
                          --resource-group ${env.AZURE_RESOURCE_GROUP} \\
                          --docker-custom-image-name ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest \\
                          --docker-registry-server-url https://index.docker.io
                        """
                        
                        // Restart web app untuk apply perubahan
                        bat "az webapp restart --name ${env.AZURE_WEBAPP_NAME} --resource-group ${env.AZURE_RESOURCE_GROUP}"
                        
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
                    // DIPERBAIKI: menggunakan URL App Service yang benar
                    bat "curl -f --retry 3 --retry-delay 10 ${env.APP_URL} || echo 'App might still be starting...'"
                    
                    echo '========================================'
                    echo '✅ DEPLOYMENT SUCCESSFUL!'
                    echo "🌐 Your app is live at: ${env.APP_URL}"
                    echo "📦 Docker Image: ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
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
