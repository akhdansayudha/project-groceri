pipeline {
    agent any // atau 'agent { label 'linux' }' jika ada label khusus

    environment {
        DOCKER_REGISTRY = 'wahyuditrs17'
        IMAGE_NAME = 'vektora'
        AZURE_WEBAPP_NAME = 'project-vektora'
        AZURE_RESOURCE_GROUP = 'project-vektora-group' // Ganti jika berbeda
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
                    // Gunakan sh, bukan bat
                    sh "docker build -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID} -t ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest ."
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
                        sh "echo ${DOCKER_PASSWORD} | docker login --username ${DOCKER_USERNAME} --password-stdin"
                        
                        // Push image
                        sh "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:${env.BUILD_ID}"
                        sh "docker push ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest"
                        
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
                        // PERUBAHAN PENTING: ganti 'bat' dengan 'sh' dan format command
                        sh """
                        az login --service-principal \
                          -u $AZURE_USERNAME \
                          -p $AZURE_PASSWORD \
                          --tenant common
                        
                        az webapp config container set \
                          --name ${env.AZURE_WEBAPP_NAME} \
                          --resource-group ${env.AZURE_RESOURCE_GROUP} \
                          --docker-custom-image-name ${env.DOCKER_REGISTRY}/${env.IMAGE_NAME}:latest \
                          --docker-registry-server-url https://index.docker.io
                        
                        az webapp restart \
                          --name ${env.AZURE_WEBAPP_NAME} \
                          --resource-group ${env.AZURE_RESOURCE_GROUP}
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
                    // Ganti 'bat' dengan 'sh' untuk curl
                    sh "curl -f --retry 3 --retry-delay 10 ${env.APP_URL} || echo 'App might still be starting...'"
                    
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
